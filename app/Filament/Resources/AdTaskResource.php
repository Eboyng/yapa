<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdTaskResource\Pages;
use App\Models\AdTask;
use App\Models\User;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\SettingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class AdTaskResource extends Resource
{
    protected static ?string $model = AdTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Ad Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('ad_id')
                            ->label('Advertisement')
                            ->relationship('ad', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                AdTask::STATUS_ACTIVE => 'Active',
                                AdTask::STATUS_PENDING_REVIEW => 'Pending Review',
                                AdTask::STATUS_APPROVED => 'Approved',
                                AdTask::STATUS_REJECTED => 'Rejected',
                                AdTask::STATUS_COMPLETED => 'Completed',
                                AdTask::STATUS_EXPIRED => 'Expired',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\TextInput::make('view_count')
                            ->label('View Count')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('earnings_amount')
                            ->label('Earnings Amount')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('₦'),
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Started At'),
                        Forms\Components\DateTimePicker::make('screenshot_uploaded_at')
                            ->label('Screenshot Uploaded At'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Review Information')
                    ->schema([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(3),
                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Reviewed At'),
                        Forms\Components\Select::make('reviewed_by_admin_id')
                            ->label('Reviewed By')
                            ->relationship('reviewedByAdmin', 'name')
                            ->searchable(),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record && in_array($record->status, [AdTask::STATUS_APPROVED, AdTask::STATUS_REJECTED])),
                
                Forms\Components\Section::make('Appeal Information')
                    ->schema([
                        Forms\Components\Textarea::make('appeal_message')
                            ->label('Appeal Message')
                            ->rows(3),
                        Forms\Components\Select::make('appeal_status')
                            ->options([
                                AdTask::APPEAL_STATUS_PENDING => 'Pending',
                                AdTask::APPEAL_STATUS_APPROVED => 'Approved',
                                AdTask::APPEAL_STATUS_REJECTED => 'Rejected',
                            ]),
                        Forms\Components\DateTimePicker::make('appeal_submitted_at')
                            ->label('Appeal Submitted At'),
                        Forms\Components\DateTimePicker::make('appeal_reviewed_at')
                            ->label('Appeal Reviewed At'),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record && $record->appeal_submitted_at),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ad.title')
                    ->label('Advertisement')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => AdTask::STATUS_ACTIVE,
                        'warning' => AdTask::STATUS_PENDING_REVIEW,
                        'success' => AdTask::STATUS_APPROVED,
                        'danger' => AdTask::STATUS_REJECTED,
                        'success' => AdTask::STATUS_COMPLETED,
                        'secondary' => AdTask::STATUS_EXPIRED,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        AdTask::STATUS_ACTIVE => 'Active',
                        AdTask::STATUS_PENDING_REVIEW => 'Pending Review',
                        AdTask::STATUS_APPROVED => 'Approved',
                        AdTask::STATUS_REJECTED => 'Rejected',
                        AdTask::STATUS_COMPLETED => 'Completed',
                        AdTask::STATUS_EXPIRED => 'Expired',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('earnings_amount')
                    ->label('Earnings')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->disk('public')
                    ->height(40)
                    ->width(40),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('screenshot_uploaded_at')
                    ->label('Screenshot Uploaded')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        AdTask::STATUS_ACTIVE => 'Active',
                        AdTask::STATUS_PENDING_REVIEW => 'Pending Review',
                        AdTask::STATUS_APPROVED => 'Approved',
                        AdTask::STATUS_REJECTED => 'Rejected',
                        AdTask::STATUS_COMPLETED => 'Completed',
                        AdTask::STATUS_EXPIRED => 'Expired',
                    ]),
                Filter::make('pending_review')
                    ->label('Pending Review Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', AdTask::STATUS_PENDING_REVIEW)),
                Filter::make('has_screenshot')
                    ->label('Has Screenshot')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('screenshot_path')),
                Filter::make('with_appeals')
                    ->label('With Appeals')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('appeal_submitted_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AdTask $record): bool => $record->status === AdTask::STATUS_PENDING_REVIEW)
                    ->requiresConfirmation()
                    ->modalHeading('Approve Ad Task')
                    ->modalDescription('This will approve the task and calculate earnings for the user.')
                    ->action(function (AdTask $record) {
                        try {
                            // Simply approve the task - the model observer will handle payment automatically
                            $record->approve(auth()->user());
                            
                            // Send notification
                            app(NotificationService::class)->sendAdTaskApprovedNotification(
                                $record->user,
                                $record,
                                $record->calculateEarnings()
                            );
                            
                            Notification::make()
                                ->title('Task Approved')
                                ->body('Ad task has been approved and earnings have been credited.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to approve task: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AdTask $record): bool => $record->status === AdTask::STATUS_PENDING_REVIEW)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please provide a reason for rejection...')
                    ])
                    ->action(function (AdTask $record, array $data) {
                        try {
                            $record->update([
                                'status' => AdTask::STATUS_REJECTED,
                                'reviewed_at' => now(),
                                'reviewed_by_admin_id' => Auth::id(),
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                            
                            // Send notification to user
                            $notificationService = app(NotificationService::class);
                            $notificationService->sendAdTaskRejectedNotification(
                                $record->user, 
                                $record, 
                                $data['rejection_reason']
                            );
                            
                            Log::info('Ad task rejected by admin', [
                                'ad_task_id' => $record->id,
                                'user_id' => $record->user_id,
                                'admin_id' => Auth::id(),
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                            
                            Notification::make()
                                ->title('Task Rejected')
                                ->body('Task has been rejected and user notified.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Log::error('Failed to reject ad task', [
                                'ad_task_id' => $record->id,
                                'error' => $e->getMessage()
                            ]);
                            
                            Notification::make()
                                ->title('Rejection Failed')
                                ->body('Failed to reject task: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAdTasks::route('/'),
            'create' => Pages\CreateAdTask::route('/create'),
            'view' => Pages\ViewAdTask::route('/{record}'),
            'edit' => Pages\EditAdTask::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', AdTask::STATUS_PENDING_REVIEW)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}