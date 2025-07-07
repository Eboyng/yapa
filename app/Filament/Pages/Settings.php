<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'System Settings';

    public ?array $data = [];

    protected ?SettingService $settingService = null;

    public function mount(): void
    {
        $this->settingService = app(SettingService::class);
        $this->form->fill($this->getSettingsData());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\Section::make('Application Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('app_name')
                                            ->label('Application Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('app_version')
                                            ->label('Application Version')
                                            ->required()
                                            ->maxLength(50),
                                        Forms\Components\Toggle::make('maintenance_mode')
                                            ->label('Maintenance Mode')
                                            ->helperText('Enable to put the application in maintenance mode'),
                                        Forms\Components\Toggle::make('registration_enabled')
                                            ->label('Registration Enabled')
                                            ->helperText('Allow new user registrations'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Contact Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('admin_contact_name')
                                            ->label('Admin Contact Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('admin_contact_number')
                                            ->label('Admin Contact Number')
                                            ->required()
                                            ->tel()
                                            ->maxLength(20),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Batch Settings')
                            ->schema([
                                Forms\Components\Section::make('Batch Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('batch_auto_close_days')
                                            ->label('Auto Close Days')
                                            ->helperText('Number of days after which batches auto-close')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(365),
                                        Forms\Components\TextInput::make('trial_batch_limit')
                                            ->label('Trial Batch Limit')
                                            ->helperText('Maximum members in trial batches')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(1000),
                                        Forms\Components\TextInput::make('regular_batch_limit')
                                            ->label('Regular Batch Limit')
                                            ->helperText('Maximum members in regular batches')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(1000),
                                    ])
                                    ->columns(3),
                                
                                Forms\Components\Section::make('Matching Weights')
                                    ->schema([
                                        Forms\Components\TextInput::make('location_weight')
                                            ->label('Location Weight (%)')
                                            ->helperText('Weight given to location matching')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('interests_weight')
                                            ->label('Interests Weight (%)')
                                            ->helperText('Weight given to interests matching')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%'),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Ad Settings')
                            ->schema([
                                Forms\Components\Section::make('Ad Feature Control')
                                    ->schema([
                                        Forms\Components\Toggle::make('ads_feature_enabled')
                                            ->label('Enable Ads Feature')
                                            ->helperText('Enable or disable the entire ads (Share & Earn) feature')
                                            ->default(true),
                                    ])
                                    ->columns(1),
                                
                                Forms\Components\Section::make('Ad Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('ad_earnings_per_view')
                                            ->label('Earnings Per View')
                                            ->helperText('Amount earned per ad view')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('ad_screenshot_wait_hours')
                                            ->label('Screenshot Wait Hours')
                                            ->helperText('Hours to wait before requiring screenshot')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(168),
                                        Forms\Components\TextInput::make('max_ad_rejection_count')
                                            ->label('Max Rejection Count')
                                            ->helperText('Maximum rejections before account suspension')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(10),
                                        Forms\Components\TextInput::make('appeal_cooldown_days')
                                            ->label('Appeal Cooldown Days')
                                            ->helperText('Days to wait before allowing appeals')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(30),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->schema([
                                Forms\Components\Section::make('Notification Settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('whatsapp_notifications_enabled')
                                            ->label('WhatsApp Notifications')
                                            ->helperText('Enable WhatsApp notifications'),
                                        Forms\Components\Toggle::make('email_notifications_enabled')
                                            ->label('Email Notifications')
                                            ->helperText('Enable email notifications'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('OTP Delivery Settings')
                                    ->schema([
                                        Forms\Components\Select::make('otp_delivery_method')
                                            ->label('Primary OTP Delivery Method')
                                            ->helperText('Choose the primary method for sending OTP codes')
                                            ->options([
                                                'whatsapp' => 'WhatsApp (with SMS fallback)',
                                                'sms' => 'SMS Only',
                                            ])
                                            ->default('whatsapp')
                                            ->required(),
                                        Forms\Components\Toggle::make('otp_sms_fallback_enabled')
                                            ->label('SMS Fallback')
                                            ->helperText('Enable SMS fallback when WhatsApp fails')
                                            ->default(true),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Kudisms WhatsApp API Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('kudisms_api_key')
                                            ->label('Kudisms API Key')
                                            ->helperText('Your Kudisms API key for WhatsApp messaging')
                                            ->password()
                                            ->revealable()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kudisms_whatsapp_template_code')
                                            ->label('WhatsApp Template Code')
                                            ->helperText('Default template code for WhatsApp messages')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kudisms_sender_id')
                                            ->label('Sender ID')
                                            ->helperText('Sender ID for WhatsApp messages')
                                            ->default('Yapa')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('kudisms_whatsapp_url')
                                            ->label('WhatsApp API URL')
                                            ->helperText('Kudisms WhatsApp API endpoint')
                                            ->default('https://my.kudisms.net/api/whatsapp')
                                            ->url()
                                            ->maxLength(255),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Kudisms SMS API Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('kudisms_sms_template_code')
                                            ->label('SMS Template Code')
                                            ->helperText('Template code for SMS OTP messages')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kudisms_app_name_code')
                                            ->label('App Name Code')
                                            ->helperText('Application name code for SMS API')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kudisms_sms_url')
                                            ->label('SMS OTP API URL')
                                            ->helperText('Kudisms SMS OTP API endpoint')
                                            ->default('https://my.kudisms.net/api/otp')
                                            ->url()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kudisms_balance_url')
                                            ->label('Balance Check API URL')
                                            ->helperText('Kudisms balance check API endpoint')
                                            ->default('https://my.kudisms.net/api/balance')
                                            ->url()
                                            ->maxLength(255),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('File Upload')
                            ->schema([
                                Forms\Components\Section::make('Upload Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_file_upload_size')
                                            ->label('Max File Upload Size (MB)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->suffix('MB'),
                                        Forms\Components\TextInput::make('supported_image_formats')
                                            ->label('Supported Image Formats')
                                            ->helperText('Comma-separated list of supported formats')
                                            ->required()
                                            ->placeholder('jpg,jpeg,png,gif'),
                                        Forms\Components\Toggle::make('vcf_export_enabled')
                                            ->label('VCF Export Enabled')
                                            ->helperText('Allow VCF file exports'),
                                        Forms\Components\Toggle::make('google_oauth_enabled')
                                            ->label('Google OAuth Enabled')
                                            ->helperText('Enable Google OAuth integration'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
            Action::make('reset')
                ->label('Reset to Defaults')
                ->color('gray')
                ->requiresConfirmation()
                ->action('resetToDefaults'),
        ];
    }

    public function save(): void
    {
        try {
            if (!$this->settingService) {
                $this->settingService = app(SettingService::class);
            }
            
            $data = $this->form->getState();
            
            foreach ($data as $key => $value) {
                $type = $this->getSettingType($key, $value);
                $this->settingService->set($key, $value, $type);
            }
            
            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
                
        } catch (Halt $exception) {
            return;
        }
    }

    public function resetToDefaults(): void
    {
        try {
            if (!$this->settingService) {
                $this->settingService = app(SettingService::class);
            }
            
            $this->settingService->resetToDefaults();
            $this->form->fill($this->getSettingsData());
            
            Notification::make()
                ->title('Settings reset to defaults')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to reset settings')
                ->danger()
                ->send();
        }
    }

    protected function getSettingsData(): array
    {
        if (!$this->settingService) {
            $this->settingService = app(SettingService::class);
        }
        
        return $this->settingService->all();
    }

    protected function getSettingType(string $key, $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_int($value)) {
            return 'integer';
        }
        
        if (is_float($value)) {
            return 'float';
        }
        
        if (is_array($value)) {
            return 'array';
        }
        
        return 'string';
    }
}