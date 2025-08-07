<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtpResource\Pages;
use App\Models\Otp;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Carbon\Carbon;

class OtpResource extends Resource
{
    protected static ?string $model = Otp::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Security';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'OTP Management';
    
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
                // OTPs are read-only for security reasons
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('identifier')
                    ->label('Identifier')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Email or phone number'),
                    
                TextColumn::make('context')
                    ->label('Context')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'registration' => 'success',
                        'login' => 'info',
                        'password_reset' => 'warning',
                        'email_verification' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.view', $record->user) : null)
                    ->placeholder('No user associated'),
                    
                IconColumn::make('verified')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->verified ? 'Verified' : 'Not verified'),
                    
                TextColumn::make('attempts')
                    ->label('Attempts')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 2 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                    
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success')
                    ->tooltip(fn ($record) => !$record->expires_at ? 'No expiration' : ($record->expires_at->isPast() ? 'Expired' : 'Valid until ' . $record->expires_at->format('M j, Y g:i A'))),
                    
                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not verified'),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('context')
                    ->options([
                        'registration' => 'Registration',
                        'login' => 'Login',
                        'password_reset' => 'Password Reset',
                        'email_verification' => 'Email Verification',
                    ])
                    ->multiple(),
                    
                TernaryFilter::make('verified')
                    ->label('Verification Status')
                    ->placeholder('All OTPs')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified'),
                    
                Filter::make('expired')
                    ->label('Expired OTPs')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->toggle(),
                    
                Filter::make('active')
                    ->label('Active OTPs')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '>', now())->where('verified', false))
                    ->toggle(),
                    
                Filter::make('high_attempts')
                    ->label('High Attempts (2+)')
                    ->query(fn (Builder $query): Builder => $query->where('attempts', '>=', 2))
                    ->toggle(),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Created from'),
                        DatePicker::make('created_until')
                            ->label('Created until'),
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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete OTP')
                    ->modalDescription('Are you sure you want to delete this OTP record? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected OTPs')
                        ->modalDescription('Are you sure you want to delete the selected OTP records? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
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
            'index' => Pages\ListOtps::route('/'),
            'view' => Pages\ViewOtp::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        $activeCount = static::getModel()::where('expires_at', '>', now())
            ->where('verified', false)
            ->count();
            
        return $activeCount > 0 ? (string) $activeCount : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}