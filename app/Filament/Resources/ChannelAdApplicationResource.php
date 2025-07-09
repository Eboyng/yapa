<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelAdApplicationResource\Pages;
use App\Models\ChannelAdApplication;
use App\Models\ChannelAd;
use App\Models\Channel;
use App\Models\Transaction;
use App\Models\AuditLog;
use App\Services\TransactionService;
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

    protected static ?string $navigationGroup = 'Channel Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Ad Applications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Application Information')
                    ->schema([
                        Forms\Components\Select::make('channel_id')
                            ->relationship('channel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('channel_ad_id')
                            ->relationship('channelAd', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('application_message')
                            ->maxLength(1000)
                            ->rows(3)
                            ->label('Application Message'),
                    ])->columns(1),
                
                Forms\Components\Section::make('Status & Proof')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                ChannelAdApplication::STATUS_PENDING => 'Pending',
                                ChannelAdApplication::STATUS_APPROVED => 'Approved',
                                ChannelAdApplication::STATUS_REJECTED => 'Rejected',
                                ChannelAdApplication::STATUS_PROOF_SUBMITTED => 'Proof Submitted',
                                ChannelAdApplication::STATUS_COMPLETED => 'Completed',
                                ChannelAdApplication::STATUS_DISPUTED => 'Disputed',
                            ])
                            ->default(ChannelAdApplication::STATUS_PENDING),
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
                Tables\Columns\TextColumn::make('channel.name')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('channelAd.title')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->label('Ad Title'),
                Tables\Columns\TextColumn::make('channelAd.payment_per_channel')
                    ->money('NGN')
                    ->sortable()
                    ->label('Payment'),
                Tables\Columns\TextColumn::make('escrow_amount')
                    ->label('Escrow')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_transaction_id')
                    ->label('Escrow TX ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ChannelAdApplication::STATUS_PENDING => 'warning',
                        ChannelAdApplication::STATUS_APPROVED => 'success',
                        ChannelAdApplication::STATUS_REJECTED => 'danger',
                        ChannelAdApplication::STATUS_PROOF_SUBMITTED => 'info',
                        ChannelAdApplication::STATUS_COMPLETED => 'success',
                        ChannelAdApplication::STATUS_DISPUTED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('proof_screenshot')
                    ->boolean()
                    ->label('Has Proof'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Applied At'),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        ChannelAdApplication::STATUS_PENDING => 'Pending',
                        ChannelAdApplication::STATUS_APPROVED => 'Approved',
                        ChannelAdApplication::STATUS_REJECTED => 'Rejected',
                        ChannelAdApplication::STATUS_PROOF_SUBMITTED => 'Proof Submitted',
                        ChannelAdApplication::STATUS_COMPLETED => 'Completed',
                        ChannelAdApplication::STATUS_DISPUTED => 'Disputed',
                    ]),
                Tables\Filters\Filter::make('has_proof')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('proof_screenshot')),
                Tables\Filters\Filter::make('disputed')
                    ->query(fn (Builder $query): Builder => $query->where('status', ChannelAdApplication::STATUS_DISPUTED)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PENDING)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->maxLength(500),
                    ])
                    ->action(function (ChannelAdApplication $record, array $data): void {
                        $record->approve($data['admin_notes'] ?? null);
                        
                        Notification::make()
                            ->title('Application Approved')
                            ->body("Application for '{$record->channelAd->title}' has been approved.")
                            ->success()
                            ->send();
                    }),
                
                Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(500),
                        Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->maxLength(500),
                    ])
                    ->action(function (ChannelAdApplication $record, array $data): void {
                        $record->reject($data['rejection_reason'], $data['admin_notes'] ?? null);
                        
                        Notification::make()
                            ->title('Application Rejected')
                            ->body("Application for '{$record->channelAd->title}' has been rejected.")
                            ->warning()
                            ->send();
                    }),
                
                Action::make('complete')
                    ->label('Complete & Release Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PROOF_SUBMITTED)
                    ->requiresConfirmation()
                    ->modalHeading('Complete Application & Release Payment')
                    ->modalDescription('This will release the escrow payment to the channel owner (90%) and admin fee (10%). Are you sure the proof is satisfactory?')
                    ->action(function (ChannelAdApplication $record): void {
                        try {
                            $record->approveProofAndReleaseEscrow();
                            
                            $channelOwnerAmount = $record->escrow_amount * 0.9;
                            $adminFee = $record->escrow_amount * 0.1;
                            
                            Notification::make()
                                ->title('Application Completed')
                                ->body("Payment released: ₦{$channelOwnerAmount} to channel owner, ₦{$adminFee} admin fee.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to complete application: ' . $e->getMessage())
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
        return static::getModel()::where('status', ChannelAdApplication::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}