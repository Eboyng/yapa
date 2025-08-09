<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingUserResource\Pages;
use App\Models\PendingUser;
use App\Models\User;
use App\Services\OtpService;
use App\Jobs\SendOtpJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PendingUserResource extends Resource
{
    protected static ?string $model = PendingUser::class;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 40;
    protected static ?string $modelLabel = 'Pending User';
    protected static ?string $pluralModelLabel = 'Pending Users';

    public static function canCreate(): bool
    {
        return false; // Disable creating new pending users from admin panel
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identity')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp_number')
                    ->tel()
                    ->required()
                    ->maxLength(32)
                    ->label('WhatsApp Number'),
            ])->columns(3),

            Forms\Components\Section::make('Security')->schema([
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(6)
                    ->maxLength(191)
                    ->helperText('Leave blank to keep existing password (already hashed).'),
            ])->columns(1),

            Forms\Components\Section::make('OTP Meta')->schema([
                Forms\Components\TextInput::make('otp_attempts')
                    ->numeric()
                    ->readOnly()
                    ->default(0),
                Forms\Components\DateTimePicker::make('otp_expires_at')
                    ->seconds(false)
                    ->label('OTP Expires At'),
                Forms\Components\TextInput::make('resend_attempts')
                    ->numeric()
                    ->readOnly()
                    ->default(0),
                Forms\Components\DateTimePicker::make('last_resend_at')
                    ->seconds(false)
                    ->readOnly()
                    ->label('Last Resend At'),
                Forms\Components\Textarea::make('failure_reason')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->seconds(false)
                    ->label('Record Expires At'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\IconColumn::make('otp_status')
                    ->label('OTP Status')
                    ->state(function (PendingUser $record) {
                        if ($record->hasOtpExpired()) {
                            return 'expired';
                        }
                        if ($record->hasMaxOtpAttempts()) {
                            return 'max_attempts';
                        }
                        return 'active';
                    })
                    ->icon(function ($state) {
                        return match ($state) {
                            'expired' => 'heroicon-o-x-circle',
                            'max_attempts' => 'heroicon-o-exclamation-triangle',
                            'active' => 'heroicon-o-check-circle',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'expired' => 'danger',
                            'max_attempts' => 'warning',
                            'active' => 'success',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('otp_attempts')
                    ->label('OTP Attempts')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 3 ? 'danger' : ($state >= 2 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('resend_attempts')
                    ->label('Resend Attempts')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 3 ? 'danger' : ($state >= 2 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('otp_expires_at')
                    ->dateTime()
                    ->sortable()
                    ->label('OTP Expires'),
                Tables\Columns\IconColumn::make('record_status')
                    ->label('Record Status')
                    ->state(fn (PendingUser $record) => $record->hasExpired() ? 'expired' : 'active')
                    ->icon(fn ($state) => $state === 'expired' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($state) => $state === 'expired' ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->label('Record Expires')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('Not Expired')
                    ->query(fn (Builder $query) => $query->notExpired())
                    ->default(),
                Tables\Filters\Filter::make('Expired')
                    ->query(fn (Builder $query) => $query->expired()),
                Tables\Filters\Filter::make('OTP Expired')
                    ->query(fn (Builder $query) => $query->whereNotNull('otp_expires_at')->where('otp_expires_at', '<', now())),
                Tables\Filters\Filter::make('Max OTP Attempts')
                    ->query(fn (Builder $query) => $query->where('otp_attempts', '>=', 3)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('regenerate_otp')
                    ->label('Regenerate OTP')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate OTP')
                    ->modalDescription('This will generate a new OTP code for this user.')
                    ->action(function (PendingUser $record) {
                        try {
                            $otp = $record->generateOtp();
                            
                            // Optional: Send OTP via WhatsApp
                            $otpService = app(OtpService::class);
                            $templates = OtpService::getMessageTemplates();
                            $result = $otpService->sendOtp(
                                $record->whatsapp_number,
                                $templates['registration'],
                                $record->email,
                                true,
                                'registration'
                            );
                            
                            if ($result['success']) {
                                Notification::make()
                                    ->title('OTP Regenerated and Sent')
                                    ->body("New OTP generated and sent via {$result['method']} to {$record->whatsapp_number}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('OTP Regenerated')
                                    ->body('New OTP generated but failed to send: ' . $result['message'])
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to regenerate OTP: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('resend_otp')
                    ->label('Resend OTP')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (PendingUser $record) => $record->canResendOtp())
                    ->requiresConfirmation()
                    ->modalHeading('Resend OTP')
                    ->modalDescription('This will resend the current OTP code to the user.')
                    ->action(function (PendingUser $record) {
                        try {
                            if (!$record->canResendOtp()) {
                                Notification::make()
                                    ->title('Cannot Resend')
                                    ->body('Resend limit reached or too soon since last resend.')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            // Update resend tracking
                            $record->increment('resend_attempts');
                            $record->update(['last_resend_at' => now()]);
                            
                            // Send OTP via WhatsApp
                            $otpService = app(OtpService::class);
                            $templates = OtpService::getMessageTemplates();
                            $result = $otpService->sendOtp(
                                $record->whatsapp_number,
                                $templates['registration'],
                                $record->email,
                                true,
                                'registration'
                            );
                            
                            if ($result['success']) {
                                Notification::make()
                                    ->title('OTP Resent')
                                    ->body("OTP resent via {$result['method']} to {$record->whatsapp_number}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Resend Failed')
                                    ->body('Failed to resend OTP: ' . $result['message'])
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to resend OTP: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('manual_verify')
                    ->label('Manual Verify')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('otp_code')
                            ->label('OTP Code')
                            ->required()
                            ->length(6)
                            ->numeric()
                            ->placeholder('Enter 6-digit OTP'),
                    ])
                    ->modalHeading('Manual OTP Verification')
                    ->modalDescription('Enter the OTP code to manually verify this user.')
                    ->action(function (PendingUser $record, array $data) {
                        try {
                            if ($record->verifyOtp($data['otp_code'])) {
                                $user = $record->convertToUser();
                                
                                Notification::make()
                                    ->title('User Verified and Converted')
                                    ->body("User {$user->name} has been successfully verified and converted.")
                                    ->success()
                                    ->send();
                                    
                                return redirect()->route('filament.admin.resources.pending-users.index');
                            } else {
                                Notification::make()
                                    ->title('Invalid OTP')
                                    ->body('The provided OTP code is invalid or expired.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to verify OTP: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('convert_to_user')
                    ->label('Convert to User')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Convert to User')
                    ->modalDescription('This will bypass OTP verification and convert this pending user to a real user account.')
                    ->action(function (PendingUser $record) {
                        try {
                            $user = $record->convertToUser();
                            
                            Notification::make()
                                ->title('User Converted Successfully')
                                ->body("Pending user has been converted to user: {$user->name} ({$user->email})")
                                ->success()
                                ->send();
                                
                            return redirect()->route('filament.admin.resources.pending-users.index');
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Conversion Failed')
                                ->body('Failed to convert user: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('convert_multiple')
                        ->label('Convert to Users')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Convert Multiple Users')
                        ->modalDescription('This will convert all selected pending users to real user accounts, bypassing OTP verification.')
                        ->action(function ($records) {
                            $converted = 0;
                            $failed = 0;
                            
                            DB::transaction(function () use ($records, &$converted, &$failed) {
                                foreach ($records as $record) {
                                    try {
                                        $record->convertToUser();
                                        $converted++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                        \Log::error('Failed to convert pending user: ' . $e->getMessage(), [
                                            'pending_user_id' => $record->id,
                                            'email' => $record->email,
                                        ]);
                                    }
                                }
                            });
                            
                            if ($converted > 0) {
                                Notification::make()
                                    ->title('Bulk Conversion Complete')
                                    ->body("Successfully converted {$converted} users." . ($failed > 0 ? " {$failed} failed." : ''))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Conversion Failed')
                                    ->body('No users were converted successfully.')
                                    ->danger()
                                    ->send();
                            }
                        }),
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
            'index' => Pages\ListPendingUsers::route('/'),
            'view' => Pages\ViewPendingUser::route('/{record}'),
            'edit' => Pages\EditPendingUser::route('/{record}/edit'),
            // Note: Create page is intentionally removed
        ];
    }
}
