<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Batch;
use App\Models\BatchMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MyBatches extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, active, closed
    public $isProcessing = false;

    protected $queryString = [
        'filter' => ['except' => 'all'],
    ];

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function downloadContacts($batchId)
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $user = Auth::user();
            $batch = Batch::with(['members.user'])->findOrFail($batchId);

            // Validate user is a member
            $membership = $batch->members()->where('user_id', $user->id)->first();
            if (!$membership) {
                $this->addError('download', 'You are not a member of this batch.');
                return;
            }

            // Check if batch is full for download
            if (!$batch->isFull()) {
                $this->addError('download', 'Batch is not yet full. Download will be available when all slots are filled.');
                return;
            }

            // Generate VCF content
            $vcfContent = $this->generateVcfContent($batch);

            // Mark member as downloaded if not already
            if (!$membership->downloaded_at) {
                $membership->markAsDownloaded();
            }

            // Return VCF file download
            return Response::streamDownload(function () use ($vcfContent) {
                echo $vcfContent;
            }, "batch_{$batch->id}_contacts.vcf", [
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to download VCF from MyBatches', [
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            $this->addError('download', 'Failed to download contacts. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $batches = $this->getMyBatches();

        return view('livewire.my-batches', [
            'batches' => $batches,
        ]);
    }

    protected function getMyBatches()
    {
        $user = Auth::user();
        $query = Batch::with(['interests', 'members'])
            ->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        // Apply filter
        switch ($this->filter) {
            case 'active':
                $query->whereIn('status', [Batch::STATUS_OPEN, Batch::STATUS_FULL]);
                break;
            case 'closed':
                $query->whereIn('status', [Batch::STATUS_CLOSED, Batch::STATUS_EXPIRED]);
                break;
            // 'all' shows everything
        }

        return $query->paginate(10);
    }

    protected function generateVcfContent(Batch $batch)
    {
        $contacts = [];
        
        // Add admin contact
        $settingService = app(\App\Services\SettingService::class);
        $adminName = $settingService->get('admin_contact_name', 'YAPA Admin');
        $adminNumber = $settingService->get('admin_contact_number', '+2348000000000');
        
        $contacts[] = [
            'name' => $adminName,
            'phone' => $adminNumber,
            'organization' => 'YAPA',
        ];

        // Get batch members
        $members = $batch->members()->with('user')->get();
        $counter = 1;
        
        foreach ($members as $member) {
            $contacts[] = [
                'name' => "yapa_{$counter}",
                'phone' => $member->formatted_whatsapp_number,
                'organization' => 'YAPA Batch',
                'note' => $member->user->location ?? '',
            ];
            $counter++;
        }

        // Simple deduplication to remove duplicate phone numbers
        $contacts = $this->deduplicateContacts($contacts);

        // Generate VCF content
        $vcfContent = "";
        foreach ($contacts as $contact) {
            $vcfContent .= "BEGIN:VCARD\r\n";
            $vcfContent .= "VERSION:3.0\r\n";
            $vcfContent .= "FN:{$contact['name']}\r\n";
            $vcfContent .= "TEL;TYPE=CELL:{$contact['phone']}\r\n";
            
            if (!empty($contact['organization'])) {
                $vcfContent .= "ORG:{$contact['organization']}\r\n";
            }
            
            if (!empty($contact['note'])) {
                $vcfContent .= "NOTE:{$contact['note']}\r\n";
            }
            
            $vcfContent .= "END:VCARD\r\n";
        }

        return $vcfContent;
    }

    protected function deduplicateContacts(array $contacts)
    {
        // Simple deduplication based on phone numbers only
        $seenNumbers = [];
        $uniqueContacts = [];
        
        foreach ($contacts as $contact) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $contact['phone']);
            
            if (!in_array($cleanNumber, $seenNumbers)) {
                $seenNumbers[] = $cleanNumber;
                $uniqueContacts[] = $contact;
            }
        }
        
        return $uniqueContacts;
    }
}