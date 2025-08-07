<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Services\EmailService;
use App\Services\NotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Voucher Management';

    protected static ?int $navigationSort = 1;
    
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Voucher Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Voucher Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('Auto-generated if empty')
                            ->helperText('Leave empty to auto-generate'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->step(0.01)
                            ->prefix('₦'),
                        
                        Forms\Components\Select::make('currency')
                            ->label('Currency')
                            ->required()
                            ->options([
                                Voucher::CURRENCY_NGN => 'Nigerian Naira (NGN)',
                                Voucher::CURRENCY_CREDITS => 'Credits',
                            ])
                            ->default(Voucher::CURRENCY_NGN),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                Voucher::STATUS_ACTIVE => 'Active',
                                Voucher::STATUS_REDEEMED => 'Redeemed',
                                Voucher::STATUS_EXPIRED => 'Expired',
                                Voucher::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(Voucher::STATUS_ACTIVE),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable()
                            ->helperText('Leave empty for no expiration'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->maxLength(500)
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('batch_id')
                            ->label('Batch ID')
                            ->nullable()
                            ->helperText('Group vouchers together'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Additional Data')
                            ->nullable()
                            ->helperText('Store additional information as key-value pairs'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Voucher::CURRENCY_NGN => 'success',
                        Voucher::CURRENCY_CREDITS => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Voucher::STATUS_ACTIVE => 'success',
                        Voucher::STATUS_REDEEMED => 'warning',
                        Voucher::STATUS_EXPIRED => 'danger',
                        Voucher::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('redeemed_by')
                    ->label('Redeemed By')
                    ->getStateUsing(fn (Voucher $record): ?string => $record->redeemedBy?->name)
                    ->placeholder('Not redeemed'),
                
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Created By')
                    ->getStateUsing(fn (Voucher $record): ?string => $record->createdBy?->name)
                    ->placeholder('System'),
                
                Tables\Columns\TextColumn::make('batch_id')
                    ->label('Batch')
                    ->searchable()
                    ->placeholder('No batch'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                
                Tables\Columns\TextColumn::make('redeemed_at')
                    ->label('Redeemed At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not redeemed'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Voucher::STATUS_ACTIVE => 'Active',
                        Voucher::STATUS_REDEEMED => 'Redeemed',
                        Voucher::STATUS_EXPIRED => 'Expired',
                        Voucher::STATUS_CANCELLED => 'Cancelled',
                    ]),
                
                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        Voucher::CURRENCY_NGN => 'NGN',
                        Voucher::CURRENCY_CREDITS => 'Credits',
                    ]),
                
                Tables\Filters\Filter::make('batch_id')
                    ->form([
                        Forms\Components\TextInput::make('batch_id')
                            ->label('Batch ID'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['batch_id'],
                            fn (Builder $query, $batchId): Builder => $query->where('batch_id', $batchId),
                        );
                    }),
                
                Tables\Filters\Filter::make('expires_soon')
                    ->label('Expires Soon (7 days)')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<=', now()->addDays(7))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('send_email')
                    ->label('Send via Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->label('Custom Message')
                            ->placeholder('Optional custom message to include with the voucher')
                            ->rows(3),
                    ])
                    ->action(function (Voucher $record, array $data): void {
                        try {
                            $emailService = app(EmailService::class);
                            $emailService->sendVoucherEmail($record, $data['email'], $data['message'] ?? null);
                            
                            Notification::make()
                                ->title('Email sent successfully')
                                ->success()
                                ->send();
                                
                            Log::info('Voucher sent via email', [
                                'voucher_id' => $record->id,
                                'email' => $data['email'],
                                'admin_id' => Auth::id(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send voucher email', [
                                'voucher_id' => $record->id,
                                'email' => $data['email'],
                                'error' => $e->getMessage(),
                            ]);
                            
                            Notification::make()
                                ->title('Failed to send email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Action::make('send_whatsapp')
                    ->label('Send via WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->helperText('Include country code (e.g., +234)'),
                        Forms\Components\Textarea::make('message')
                            ->label('Custom Message')
                            ->placeholder('Optional custom message to include with the voucher')
                            ->rows(3),
                    ])
                    ->action(function (Voucher $record, array $data): void {
                        try {
                            $notificationService = app(NotificationService::class);
                            $notificationService->sendVoucherWhatsApp($record, $data['phone'], $data['message'] ?? null);
                            
                            Notification::make()
                                ->title('WhatsApp message sent successfully')
                                ->success()
                                ->send();
                                
                            Log::info('Voucher sent via WhatsApp', [
                                'voucher_id' => $record->id,
                                'phone' => $data['phone'],
                                'admin_id' => Auth::id(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send voucher WhatsApp', [
                                'voucher_id' => $record->id,
                                'phone' => $data['phone'],
                                'error' => $e->getMessage(),
                            ]);
                            
                            Notification::make()
                                ->title('Failed to send WhatsApp message')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Action::make('send_sms')
                    ->label('Send via SMS')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->helperText('Include country code (e.g., +234)'),
                        Forms\Components\Textarea::make('message')
                            ->label('Custom Message')
                            ->placeholder('Optional custom message to include with the voucher')
                            ->rows(3),
                    ])
                    ->action(function (Voucher $record, array $data): void {
                        try {
                            $notificationService = app(NotificationService::class);
                            $notificationService->sendVoucherSMS($record, $data['phone'], $data['message'] ?? null);
                            
                            Notification::make()
                                ->title('SMS sent successfully')
                                ->success()
                                ->send();
                                
                            Log::info('Voucher sent via SMS', [
                                'voucher_id' => $record->id,
                                'phone' => $data['phone'],
                                'admin_id' => Auth::id(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send voucher SMS', [
                                'voucher_id' => $record->id,
                                'phone' => $data['phone'],
                                'error' => $e->getMessage(),
                            ]);
                            
                            Notification::make()
                                ->title('Failed to send SMS')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    BulkAction::make('export_pdf')
                        ->label('Export as PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function (Collection $records): \Symfony\Component\HttpFoundation\Response {
                            try {
                                $pdf = Pdf::loadView('vouchers.pdf', [
                                    'vouchers' => $records,
                                    'generated_at' => now(),
                                    'generated_by' => Auth::user()->name,
                                ]);
                                
                                $filename = 'vouchers_' . now()->format('Y-m-d_H-i-s') . '.pdf';
                                
                                Log::info('Vouchers exported to PDF', [
                                    'voucher_count' => $records->count(),
                                    'admin_id' => Auth::id(),
                                    'filename' => $filename,
                                ]);
                                
                                return Response::streamDownload(
                                    fn () => print($pdf->output()),
                                    $filename,
                                    ['Content-Type' => 'application/pdf']
                                );
                            } catch (\Exception $e) {
                                Log::error('Failed to export vouchers to PDF', [
                                    'error' => $e->getMessage(),
                                    'admin_id' => Auth::id(),
                                ]);
                                
                                Notification::make()
                                    ->title('Failed to export PDF')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                                    
                                throw $e;
                            }
                        }),
                    
                    BulkAction::make('cancel_vouchers')
                        ->label('Cancel Vouchers')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            try {
                                $voucherService = app(VoucherService::class);
                                $cancelledCount = 0;
                                
                                foreach ($records as $voucher) {
                                    if ($voucher->canBeRedeemed()) {
                                        $voucherService->cancelVoucher($voucher->id);
                                        $cancelledCount++;
                                    }
                                }
                                
                                Notification::make()
                                    ->title("Cancelled {$cancelledCount} vouchers")
                                    ->success()
                                    ->send();
                                    
                                Log::info('Bulk voucher cancellation', [
                                    'cancelled_count' => $cancelledCount,
                                    'total_selected' => $records->count(),
                                    'admin_id' => Auth::id(),
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Failed to cancel vouchers', [
                                    'error' => $e->getMessage(),
                                    'admin_id' => Auth::id(),
                                ]);
                                
                                Notification::make()
                                    ->title('Failed to cancel vouchers')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->headerActions([
                Action::make('generate_batch')
                    ->label('Generate Voucher Batch')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('count')
                            ->label('Number of Vouchers')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(10)
                            ->helperText('Maximum 100 vouchers per batch'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount per Voucher')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->step(0.01)
                            ->prefix('₦'),
                        
                        Forms\Components\Select::make('currency')
                            ->label('Currency')
                            ->required()
                            ->options([
                                Voucher::CURRENCY_NGN => 'Nigerian Naira (NGN)',
                                Voucher::CURRENCY_CREDITS => 'Credits',
                            ])
                            ->default(Voucher::CURRENCY_NGN),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable()
                            ->helperText('Leave empty for no expiration'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Optional description for all vouchers in this batch'),
                    ])
                    ->action(function (array $data): void {
                        try {
                            $voucherService = app(VoucherService::class);
                            $batchId = 'BATCH_' . now()->format('YmdHis') . '_' . Auth::id();
                            
                            $vouchers = $voucherService->generateBatch(
                                $data['count'],
                                $data['amount'],
                                $data['currency'],
                                $data['expires_at'] ? Carbon::parse($data['expires_at']) : null,
                                $data['description'] ?? null,
                                $batchId,
                                Auth::id()
                            );
                            
                            Notification::make()
                                ->title('Voucher batch generated successfully')
                                ->body("Generated {$data['count']} vouchers with batch ID: {$batchId}")
                                ->success()
                                ->send();
                                
                            Log::info('Voucher batch generated', [
                                'batch_id' => $batchId,
                                'count' => $data['count'],
                                'amount' => $data['amount'],
                                'currency' => $data['currency'],
                                'admin_id' => Auth::id(),
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to generate voucher batch', [
                                'data' => $data,
                                'error' => $e->getMessage(),
                                'admin_id' => Auth::id(),
                            ]);
                            
                            Notification::make()
                                ->title('Failed to generate voucher batch')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Action::make('export_all_pdf')
                    ->label('Export All as PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('status_filter')
                            ->label('Filter by Status')
                            ->options([
                                'all' => 'All Vouchers',
                                Voucher::STATUS_ACTIVE => 'Active Only',
                                Voucher::STATUS_REDEEMED => 'Redeemed Only',
                                Voucher::STATUS_EXPIRED => 'Expired Only',
                                Voucher::STATUS_CANCELLED => 'Cancelled Only',
                            ])
                            ->default('all'),
                    ])
                    ->action(function (array $data): \Symfony\Component\HttpFoundation\Response {
                        try {
                            $query = Voucher::query();
                            
                            if ($data['status_filter'] !== 'all') {
                                $query->where('status', $data['status_filter']);
                            }
                            
                            $vouchers = $query->with(['redeemedBy', 'createdBy'])->get();
                            
                            $pdf = Pdf::loadView('vouchers.pdf', [
                                'vouchers' => $vouchers,
                                'generated_at' => now(),
                                'generated_by' => Auth::user()->name,
                                'filter' => $data['status_filter'],
                            ]);
                            
                            $filename = 'all_vouchers_' . $data['status_filter'] . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
                            
                            Log::info('All vouchers exported to PDF', [
                                'voucher_count' => $vouchers->count(),
                                'filter' => $data['status_filter'],
                                'admin_id' => Auth::id(),
                                'filename' => $filename,
                            ]);
                            
                            return Response::streamDownload(
                                fn () => print($pdf->output()),
                                $filename,
                                ['Content-Type' => 'application/pdf']
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to export all vouchers to PDF', [
                                'error' => $e->getMessage(),
                                'admin_id' => Auth::id(),
                            ]);
                            
                            Notification::make()
                                ->title('Failed to export PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                                
                            throw $e;
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'view' => Pages\ViewVoucher::route('/{record}'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}