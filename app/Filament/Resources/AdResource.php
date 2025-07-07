<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ad Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('url')
                            ->label('Target URL')
                            ->url()
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Ad Image')
                            ->image()
                            ->directory('ads')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('video_url')
                            ->label('Ad Video')
                            ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mov'])
                            ->directory('ads/videos')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'banner' => 'Banner',
                                'video' => 'Video',
                                'text' => 'Text',
                                'interactive' => 'Interactive',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('₦'),
                        Forms\Components\TextInput::make('cost_per_view')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('₦'),
                        Forms\Components\TextInput::make('max_views')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('duration')
                            ->label('Duration (seconds)')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('For video ads only'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Scheduling')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('End Date')
                            ->after('start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Targeting')
                    ->schema([
                        Forms\Components\Select::make('target_audience')
                            ->options([
                                'all' => 'All Users',
                                'new_users' => 'New Users',
                                'active_users' => 'Active Users',
                                'premium_users' => 'Premium Users',
                            ])
                            ->multiple()
                            ->default(['all']),
                        Forms\Components\KeyValue::make('targeting_criteria')
                            ->label('Additional Targeting')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'banner' => 'primary',
                        'video' => 'success',
                        'text' => 'info',
                        'interactive' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'active' => 'success',
                        'paused' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_view')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_views')
                    ->label('Max Views')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'banner' => 'Banner',
                        'video' => 'Video',
                        'text' => 'Text',
                        'interactive' => 'Interactive',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Ad $record): bool => in_array($record->status, ['pending', 'draft']))
                    ->requiresConfirmation()
                    ->action(function (Ad $record): void {
                        $record->update(['status' => 'approved']);
                        
                        // Log the approval in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'ad_approved',
                            'details' => [
                                'ad_id' => $record->id,
                                'title' => $record->title,
                                'type' => $record->type,
                                'approved_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Ad approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Ad $record): bool => in_array($record->status, ['pending', 'draft']))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Ad $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'targeting_criteria' => array_merge($record->targeting_criteria ?? [], [
                                'rejection_reason' => $data['reason'],
                                'rejected_by' => Auth::user()->name,
                                'rejected_at' => now(),
                            ]),
                        ]);
                        
                        // Log the rejection in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'ad_rejected',
                            'details' => [
                                'ad_id' => $record->id,
                                'title' => $record->title,
                                'type' => $record->type,
                                'reason' => $data['reason'],
                                'rejected_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Ad rejected successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('activate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (Ad $record): bool => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (Ad $record): void {
                        $record->update(['status' => 'active']);
                        
                        // Log the activation in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'ad_activated',
                            'details' => [
                                'ad_id' => $record->id,
                                'title' => $record->title,
                                'activated_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Ad activated successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn (Ad $record): bool => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (Ad $record): void {
                        $record->update(['status' => 'paused']);
                        
                        // Log the pause in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'ad_paused',
                            'details' => [
                                'ad_id' => $record->id,
                                'title' => $record->title,
                                'paused_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Ad paused successfully')
                            ->success()
                            ->send();
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'view' => Pages\ViewAd::route('/{record}'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}