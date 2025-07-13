<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelAdApplicationResource\Pages;
use App\Models\ChannelAdApplication;
use App\Models\ChannelAd;
use App\Models\Channel;
use App\Models\Transaction;
use App\Models\AuditLog;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

class ChannelAdApplicationResource extends Resource
{
    protected static ?string $model = ChannelAdApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Channel Ads';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Channel Ad Applications';

    protected static ?string $modelLabel = 'Channel Ad Application';

    protected static ?string $pluralModelLabel = 'Channel Ad Applications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Application Information')
                    ->schema([
                        Forms\Components\Select::make('channel_ad_id')
                            ->label('Channel Ad')
                            ->relationship('channelAd', 'channel_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('advertiser_id')
                            ->label('Advertiser')
                            ->relationship('advertiser', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Textarea::make('application_message')
                            ->maxLength(1000)
                            ->rows(3)
                            ->label('Application Message'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->after('start_date'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('₦')
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Status & Proof')
                    ->schema([
                        Forms\Components\Select::make('booking_status')
                            ->label('Booking Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'canceled' => 'Canceled',
                            ])
                            ->default('pending'),
                        
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'held' => 'Held',
                                'released' => 'Released',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending'),
                        Forms\Components\FileUpload::make('proof_screenshot')
                            ->image()
                            ->maxSize(5120)
                            ->label('Proof Screenshot'),
                        Forms\Components\Textarea::make('proof_description')
                            ->maxLength(500)
                            ->rows(2)
                            ->label('Proof Description'),
                        Forms\Components\TextInput::make('escrow_transaction_id')
                             ->label('Escrow Transaction ID')
                             ->disabled()
                             ->dehydrated(false)
                             ->visible(fn ($record) => $record && $record->escrow_transaction_id),
                         Forms\Components\Placeholder::make('payment_breakdown')
                             ->label('Payment Breakdown')
                             ->content(function ($record) {
                                 if (!$record || !$record->escrow_amount) {
                                     return 'N/A';
                                 }
                                 $channelOwnerAmount = $record->escrow_amount * 0.9;
                                 $adminFee = $record->escrow_amount * 0.1;
                                 return "Channel Owner: ₦{$channelOwnerAmount} | Admin Fee (10%): ₦{$adminFee}";
                             })
                             ->visible(fn ($record) => $record && $record->escrow_amount),
                    ])->columns(1),
                
                Forms\Components\Section::make('Admin Notes')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->maxLength(500)
                            ->rows(2),
                    ])->columns(1),
                
                Forms\Components\Section::make('Dispute Information')
                    ->schema([
                        Forms\Components\Textarea::make('dispute_reason')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\FileUpload::make('dispute_evidence')
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(5120),
                        Forms\Components\Textarea::make('dispute_resolution')
                            ->maxLength(1000)
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('channelAd.channel_name')
                    ->label('Channel')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('advertiser.name')
                    ->label('Advertiser')
                    ->sortable()
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('booking_status')
                    ->label('Booking Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'canceled',
                    ])
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'held',
                        'success' => 'released',
                        'danger' => 'refunded',
                    ])
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('escrowTransaction.reference')
                    ->label('Escrow Txn')
                    ->limit(15)
                    ->tooltip(function (ChannelAdApplication $record): ?string {
                        return $record->escrowTransaction?->reference;
                    }),
                
                Tables\Columns\IconColumn::make('proof_screenshot')
                    ->boolean()
                    ->label('Has Proof')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('booking_status')
                    ->label('Booking Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'held' => 'Held',
                        'released' => 'Released',
                        'refunded' => 'Refunded',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Applied From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Applied Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                
                Tables\Filters\Filter::make('has_proof')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('proof_screenshot')),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ChannelAdApplication $record): bool => 
                        $record->booking_status === 'pending' && $record->payment_status === 'held'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve Application')
                    ->modalDescription('Are you sure you want to approve this application? This will release the funds to the channel owner.')
                    ->action(function (ChannelAdApplication $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                // Update application status
                                $record->update([
                                    'booking_status' => 'confirmed',
                                    'approved_at' => now(),
                                ]);

                                // Release funds through TransactionService
                                $transactionService = app(TransactionService::class);
                                $transactionService->releaseAdFunds($record);
                            });

                            Notification::make()
                                ->title('Application Approved')
                                ->body('The application has been approved and funds have been released.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Admin approval error', [
                                'application_id' => $record->id,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Approval Failed')
                                ->body('Failed to approve application: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ChannelAdApplication $record): bool => 
                        $record->booking_status === 'pending'
                    )
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please provide a reason for rejecting this application...'),
                    ])
                    ->action(function (ChannelAdApplication $record, array $data) {
                        try {
                            DB::transaction(function () use ($record, $data) {
                                // Update application with rejection details
                                $record->update([
                                    'booking_status' => 'canceled',
                                    'rejection_reason' => $data['rejection_reason'],
                                    'rejected_at' => now(),
                                ]);

                                // Process refund through TransactionService
                                $transactionService = app(TransactionService::class);
                                $transactionService->refundAd($record);
                            });

                            Notification::make()
                                ->title('Application Rejected')
                                ->body('The application has been rejected and refund has been processed.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Admin rejection error', [
                                'application_id' => $record->id,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Rejection Failed')
                                ->body('Failed to reject application: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (ChannelAdApplication $record): bool => 
                        in_array($record->booking_status, ['confirmed', 'completed']) && 
                        $record->payment_status === 'released'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Process Refund')
                    ->modalDescription('Are you sure you want to process a refund for this application? This action cannot be undone.')
                    ->action(function (ChannelAdApplication $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                // Process refund through TransactionService
                                $transactionService = app(TransactionService::class);
                                $transactionService->refundAd($record);
                            });

                            Notification::make()
                                ->title('Refund Processed')
                                ->body('The refund has been processed successfully.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Admin refund error', [
                                'application_id' => $record->id,
                                'error' => $e->getMessage(),
                            ]);

                            Notification::make()
                                ->title('Refund Failed')
                                ->body('Failed to process refund: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Application Details')
                    ->schema([
                        TextEntry::make('channel.name')
                            ->label('Channel'),
                        TextEntry::make('channel.follower_count')
                            ->label('Channel Followers')
                            ->formatStateUsing(fn ($state) => number_format($state)),
                        TextEntry::make('channelAd.title')
                            ->label('Ad Title'),
                        TextEntry::make('channelAd.payment_per_channel')
                            ->label('Payment Amount')
                            ->money('NGN'),
                        TextEntry::make('application_message')
                            ->label('Application Message'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                ChannelAdApplication::STATUS_PENDING => 'warning',
                                ChannelAdApplication::STATUS_APPROVED => 'success',
                                ChannelAdApplication::STATUS_REJECTED => 'danger',
                                ChannelAdApplication::STATUS_PROOF_SUBMITTED => 'info',
                                ChannelAdApplication::STATUS_COMPLETED => 'success',
                                ChannelAdApplication::STATUS_DISPUTED => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),
                
                Section::make('Proof of Work')
                    ->schema([
                        ImageEntry::make('proof_screenshot')
                            ->label('Proof Screenshot'),
                        TextEntry::make('proof_description')
                            ->label('Proof Description'),
                        TextEntry::make('proof_submitted_at')
                            ->label('Proof Submitted At')
                            ->dateTime(),
                    ])->columns(1)
                    ->visible(fn (ChannelAdApplication $record): bool => !empty($record->proof_screenshot)),
                
                Section::make('Admin Information')
                    ->schema([
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes'),
                        TextEntry::make('rejection_reason')
                            ->label('Rejection Reason'),
                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime(),
                        TextEntry::make('completed_at')
                            ->label('Completed At')
                            ->dateTime(),
                    ])->columns(2),
                
                Section::make('Dispute Information')
                    ->schema([
                        TextEntry::make('dispute_reason')
                            ->label('Dispute Reason'),
                        TextEntry::make('dispute_resolution')
                            ->label('Dispute Resolution'),
                        TextEntry::make('disputed_at')
                            ->label('Disputed At')
                            ->dateTime(),
                        TextEntry::make('dispute_resolved_at')
                            ->label('Dispute Resolved At')
                            ->dateTime(),
                    ])->columns(1)
                    ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_DISPUTED || !empty($record->dispute_reason)),
                
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Applied At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(2),
            ]);
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
            'index' => Pages\ListChannelAdApplications::route('/'),
            'create' => Pages\CreateChannelAdApplication::route('/create'),
            'view' => Pages\ViewChannelAdApplication::route('/{record}'),
            'edit' => Pages\EditChannelAdApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('booking_status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}