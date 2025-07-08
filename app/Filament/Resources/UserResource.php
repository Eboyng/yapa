<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\WalletService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bvn')
                            ->maxLength(11)
                            ->password()
                            ->revealable(),
                    ])->columns(2),

                Forms\Components\Section::make('Wallet Management')
                    ->schema([
                        Forms\Components\Placeholder::make('credits_wallet_balance')
                            ->label('Credits Balance')
                            ->content(fn (User $record): string => number_format($record->getCreditWallet()->balance) . ' credits'),
                        Forms\Components\Placeholder::make('naira_wallet_balance')
                            ->label('Naira Balance')
                            ->content(fn (User $record): string => '₦' . number_format($record->getNairaWallet()->balance, 2)),
                        Forms\Components\Placeholder::make('earnings_wallet_balance')
                            ->label('Earnings Balance')
                            ->content(fn (User $record): string => '₦' . number_format($record->getEarningsWallet()->balance, 2)),
                    ])->columns(3),

                Forms\Components\Section::make('Status & Flags')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Admin User'),
                        Forms\Components\Toggle::make('is_flagged_for_ads')
                            ->label('Flagged for Ads'),
                        Forms\Components\Toggle::make('whatsapp_notifications_enabled')
                            ->label('WhatsApp Notifications')
                            ->default(true),
                        Forms\Components\Toggle::make('email_notifications_enabled')
                            ->label('Email Notifications')
                            ->default(true),
                        Forms\Components\Toggle::make('email_verification_enabled')
                            ->label('Email Verification Enabled')
                            ->default(true),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Assign roles to this user'),
                        Forms\Components\TextInput::make('avatar')
                            ->label('Avatar URL')
                            ->url()
                            ->helperText('Enter the avatar URL for this user'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('appeal_message')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\TextInput::make('ad_rejection_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('otp_attempts')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('credits_wallet_balance')
                    ->label('Credits')
                    ->getStateUsing(fn (User $record): float => $record->getCreditWallet()->balance)
                    ->numeric()
                    ->suffix(' credits'),
                Tables\Columns\TextColumn::make('naira_wallet_balance')
                    ->label('Naira')
                    ->getStateUsing(fn (User $record): float => $record->getNairaWallet()->balance)
                    ->money('NGN'),
                Tables\Columns\TextColumn::make('earnings_wallet_balance')
                    ->label('Earnings')
                    ->getStateUsing(fn (User $record): float => $record->getEarningsWallet()->balance)
                    ->money('NGN'),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_flagged_for_ads')
                    ->boolean(),
                Tables\Columns\IconColumn::make('whatsapp_notifications_enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('info')
                    ->label('Roles')
                    ->limitList(2)
                    ->expandableLimitedList(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->label('Avatar'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Admin Users'),
                Tables\Filters\TernaryFilter::make('is_flagged_for_ads')
                    ->label('Flagged for Ads'),
                Tables\Filters\TernaryFilter::make('whatsapp_notifications_enabled')
                    ->label('WhatsApp Notifications'),
                Tables\Filters\Filter::make('email_verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('whatsapp_verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('whatsapp_verified_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('flag_for_spam')
                    ->icon('heroicon-o-flag')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Flag User for Spam')
                    ->modalDescription('Are you sure you want to flag this user for spam? This will prevent them from participating in ads.')
                    ->action(function (User $record) {
                        $record->update([
                            'is_flagged_for_ads' => true,
                            'flagged_at' => now(),
                        ]);
                        
                        AuditLog::log(
                            Auth::id(),
                            'user_flagged_for_spam',
                            $record->id,
                            ['is_flagged_for_ads' => false],
                            ['is_flagged_for_ads' => true],
                            'User flagged for spam via admin panel'
                        );
                        
                        Notification::make()
                            ->title('User flagged successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (User $record): bool => !$record->is_flagged_for_ads),
                    
                Action::make('unflag_user')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unflag User')
                    ->modalDescription('Are you sure you want to remove the spam flag from this user?')
                    ->action(function (User $record) {
                        $record->update([
                            'is_flagged_for_ads' => false,
                            'flagged_at' => null,
                        ]);
                        
                        AuditLog::log(
                            Auth::id(),
                            'user_unflagged',
                            $record->id,
                            ['is_flagged_for_ads' => true],
                            ['is_flagged_for_ads' => false],
                            'User unflagged via admin panel'
                        );
                        
                        Notification::make()
                            ->title('User unflagged successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (User $record): bool => $record->is_flagged_for_ads),
                    
                Action::make('fund_wallet')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('wallet_type')
                            ->label('Wallet Type')
                            ->options([
                                'credits' => 'Credits',
                                'naira' => 'Naira',
                                'earnings' => 'Earnings',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data) {
                        $walletService = app(WalletService::class);
                        try {
                            $transaction = $walletService->fundWallet(
                                $record,
                                $data['wallet_type'],
                                $data['amount'],
                                $data['description']
                            );
                            
                            Notification::make()
                                ->title('Wallet funded successfully')
                                ->body("Added {$data['amount']} to {$record->name}'s {$data['wallet_type']} wallet")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error funding wallet')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Action::make('deduct_wallet')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('wallet_type')
                            ->label('Wallet Type')
                            ->options([
                                'credits' => 'Credits',
                                'naira' => 'Naira',
                                'earnings' => 'Earnings',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Deduct from Wallet')
                    ->modalDescription('Are you sure you want to deduct funds from this user\'s wallet?')
                    ->action(function (User $record, array $data) {
                        $walletService = app(WalletService::class);
                        try {
                            $transaction = $walletService->deductWallet(
                                $record,
                                $data['wallet_type'],
                                $data['amount'],
                                $data['description']
                            );
                            
                            Notification::make()
                                ->title('Wallet deducted successfully')
                                ->body("Deducted {$data['amount']} from {$record->name}'s {$data['wallet_type']} wallet")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error deducting from wallet')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Action::make('assign_roles')
                    ->icon('heroicon-o-shield-check')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->options(Role::all()->pluck('name', 'id'))
                            ->default(fn (User $record) => $record->roles->pluck('id')->toArray())
                            ->helperText('Select roles to assign to this user'),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->syncRoles($data['roles'] ?? []);
                        
                        AuditLog::create([
                            'admin_user_id' => Auth::id(),
                            'target_user_id' => $record->id,
                            'action' => 'roles_assigned',
                            'description' => "Roles assigned to {$record->name}",
                            'metadata' => [
                                'roles' => Role::whereIn('id', $data['roles'] ?? [])->pluck('name')->toArray(),
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Roles assigned successfully')
                            ->body("Roles have been updated for {$record->name}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}