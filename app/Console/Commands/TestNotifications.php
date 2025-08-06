<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Transaction;
use App\Notifications\TransactionNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestNotifications extends Command
{
    protected $signature = 'test:notifications {--user-id=}';
    protected $description = 'Test notification system by creating sample notifications';

    public function handle()
    {
        $this->info('Testing notification system...');
        
        // Get user to test with
        $userId = $this->option('user-id');
        $user = $userId ? User::find($userId) : User::first();
        
        if (!$user) {
            $this->error('No user found to test with.');
            return 1;
        }
        
        $this->info("Testing notifications for user: {$user->name} ({$user->email})");
        
        // Create a mock transaction for testing
        $transaction = new Transaction();
        $transaction->id = 999999;
        $transaction->user_id = $user->id;
        $transaction->reference = 'TEST-' . time();
        $transaction->amount = 100.00;
        $transaction->type = 'credit';
        $transaction->category = 'test';
        $transaction->status = 'completed';
        $transaction->description = 'Test transaction for notification testing';
        $transaction->created_at = now();
        $transaction->updated_at = now();
        $transaction->exists = true; // Mark as existing to avoid save issues
        
        // Test Laravel notification (for notification bell)
        try {
            $user->notify(new TransactionNotification(
                $transaction,
                'credited',
                'Test Credit Notification',
                'Your account has been credited with ₦100.00 for testing purposes.'
            ));
            $this->info('✓ Laravel notification created successfully');
        } catch (\Exception $e) {
            $this->error('✗ Failed to create Laravel notification: ' . $e->getMessage());
        }
        
        // Check if Laravel notification was created in database
        // Note: Laravel notifications use a different table structure
        try {
            $laravelNotificationCount = $user->notifications()->count();
            $this->info("Total Laravel notifications for user: {$laravelNotificationCount}");
            
            $unreadLaravelCount = $user->unreadNotifications()->count();
            $this->info("Unread Laravel notifications for user: {$unreadLaravelCount}");
        } catch (\Exception $e) {
            $this->error('Laravel notifications table may not exist: ' . $e->getMessage());
        }
        
        // Test custom Notification model
        try {
            \App\Models\Notification::createNotification(
                $user->id,
                \App\Models\Notification::TYPE_TRANSACTION_SUCCESS,
                'Test Custom Notification',
                'This is a test notification using the custom Notification model.',
                [
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'test' => true
                ]
            );
            $this->info('✓ Custom notification created successfully');
        } catch (\Exception $e) {
            $this->error('✗ Failed to create custom notification: ' . $e->getMessage());
        }
        
        // Check custom notifications
        $customNotificationCount = \App\Models\Notification::where('user_id', $user->id)->count();
        $this->info("Total custom notifications for user: {$customNotificationCount}");
        
        $this->info('\nNotification test completed!');
        $this->info('You can now check the notification bell in the header to see if notifications appear.');
        
        return 0;
    }
}
