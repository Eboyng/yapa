<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 20;
    protected static ?string $title = 'System Settings';
    protected static ?string $slug = 'settings';
    
    public ?array $data = [];
    protected ?SettingService $settingService = null;

    // =============================
    // Authorization
    // =============================
    
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    // =============================
    // Initialization
    // =============================
    
    public function mount(): void
    {
        $this->settingService = app(SettingService::class);
        $this->form->fill($this->getSettingsData());
    }

    // =============================
    // Main Form Configuration
    // =============================
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        // Core Settings Group
                        $this->getGeneralSettingsTab(),
                        $this->getBrandingTab(),
                        $this->getMaintenanceTab(),
                        
                        // Communication Group
                        $this->getCommunicationTab(),
                        
                        // Payment & Commerce Group
                        $this->getPaymentTab(),
                        
                        // Features Group
                        $this->getFeaturesTab(),
                        
                        // Marketing Group
                        $this->getMarketingTab(),
                        
                        // System Group
                        $this->getSystemTab(),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    // =============================
    // Tab Definitions
    // =============================
    
    protected function getGeneralSettingsTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('General')
            ->icon('heroicon-o-home')
            ->badge('Core')
            ->badgeColor('primary')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make('Application Identity')
                            ->description('Core application settings and identification')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\TextInput::make('app_name')
                                    ->label('Application Name')
                                    ->maxLength(255)
                                    ->autocomplete(false)
                                    ->placeholder('My Application'),
                                    
                                Forms\Components\TextInput::make('app_version')
                                    ->label('Application Version')
                                    ->maxLength(50)
                                    ->placeholder('1.0.0')
                                    ->helperText('Semantic versioning recommended (e.g., 1.0.0)'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                        
                        Forms\Components\Section::make('Registration & Access Control')
                            ->description('Control user registration and access')
                            ->icon('heroicon-o-user-plus')
                            ->schema([
                                Forms\Components\Toggle::make('registration_enabled')
                                    ->label('Allow New Registrations')
                                    ->helperText('Enable/disable new user registrations globally')
                                    ->inline(false),
                                    
                                Forms\Components\Toggle::make('email_verification_required')
                                    ->label('Require Email Verification')
                                    ->helperText('Users must verify email before accessing the system')
                                    ->inline(false),
                                    
                                Forms\Components\TextInput::make('registration_bonus_credits')
                                    ->label('Registration Bonus Credits')
                                    ->numeric()
                                    ->default(100)
                                    ->minValue(0)
                                    ->maxValue(1000)
                                    ->suffix('credits')
                                    ->helperText('Credits awarded to new users upon registration'),
                            ])
                            ->columns(3)
                            ->collapsible(),
                        
                        Forms\Components\Section::make('Administrative Contact')
                            ->description('Contact information for system administration')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('admin_contact_name')
                                    ->label('Administrator Name')
                                    ->maxLength(255)
                                    ->placeholder('John Doe'),
                                    
                                Forms\Components\TextInput::make('admin_contact_number')
                                    ->label('Contact Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+234 xxx xxx xxxx'),
                                    
                                Forms\Components\TextInput::make('admin_contact_email')
                                    ->label('Contact Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('admin@example.com'),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ])
                    ->columns(1),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_general')
                        ->label('Save General Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveGeneralSettings')
                        ->keyBindings(['mod+shift+g']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getBrandingTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Branding')
            ->icon('heroicon-o-paint-brush')
            ->schema([
                Forms\Components\Section::make('Brand Identity')
                    ->description('Configure your site\'s visual identity and branding')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Site Name')
                                    ->maxLength(255)
                                    ->placeholder('My Site')
                                    ->helperText('Displayed in browser title and throughout the site')
                                    ->columnSpan(1),
                                    
                                Forms\Components\TextInput::make('site_logo_name')
                                    ->label('Logo Text')
                                    ->maxLength(255)
                                    ->placeholder('MySite')
                                    ->helperText('Alternative text when logo image is not available')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\FileUpload::make('site_logo')
                                    ->label('Site Logo')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->directory('branding')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                                    ->maxSize(2048)
                                    ->helperText('Recommended: SVG or PNG with transparent background (Max 2MB)')
                                    ->columnSpan(1),
                                    
                                Forms\Components\FileUpload::make('site_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('branding')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                    ->maxSize(512)
                                    ->helperText('16x16 or 32x32 pixels recommended (Max 512KB)')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                            
                        Forms\Components\ColorPicker::make('brand_primary_color')
                            ->label('Primary Brand Color')
                            ->helperText('Main brand color used throughout the application'),
                            
                        Forms\Components\ColorPicker::make('brand_secondary_color')
                            ->label('Secondary Brand Color')
                            ->helperText('Accent color for highlights and secondary elements'),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_branding')
                        ->label('Save Branding Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveBrandingSettings')
                        ->keyBindings(['mod+shift+b']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getMaintenanceTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Maintenance')
            ->icon('heroicon-o-wrench-screwdriver')
            ->schema([
                Forms\Components\Section::make('Maintenance Mode')
                    ->description('Control site availability and maintenance messaging')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        Forms\Components\Toggle::make('maintenance_mode')
                            ->label('Enable Maintenance Mode')
                            ->helperText('Restrict site access to allowed IPs only')
                            ->live()
                            ->afterStateUpdated(fn ($state) => $state 
                                ? Notification::make()
                                    ->warning()
                                    ->title('Maintenance Mode')
                                    ->body('Remember to add your IP to the allowed list!')
                                    ->send()
                                : null
                            ),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\RichEditor::make('maintenance_message')
                                    ->label('Maintenance Message')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'link',
                                        'bulletList',
                                    ])
                                    ->maxLength(500)
                                    ->helperText('Message displayed to users during maintenance')
                                    ->default('We are currently performing scheduled maintenance. We\'ll be back shortly!')
                                    ->columnSpan(2),
                                
                                Forms\Components\DateTimePicker::make('maintenance_end_time')
                                    ->label('Estimated End Time')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->helperText('Shows countdown timer to users')
                                    ->minDate(now())
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('maintenance_mode')),
                        
                        Forms\Components\Textarea::make('maintenance_allowed_ips')
                            ->label('Allowed IP Addresses')
                            ->rows(4)
                            ->helperText('One IP per line. Your current IP: ' . request()->ip())
                            ->placeholder("192.168.1.1\n203.0.113.1\n" . request()->ip())
                            ->visible(fn (Forms\Get $get) => $get('maintenance_mode')),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_maintenance')
                        ->label('Save Maintenance Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveMaintenanceSettings')
                        ->keyBindings(['mod+shift+m']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getCommunicationTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Communication')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->badge('Important')
            ->badgeColor('warning')
            ->schema([
                // Email Configuration
                Forms\Components\Section::make('Email Configuration')
                    ->description('Configure email delivery settings')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Forms\Components\Select::make('mail_mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'postmark' => 'Postmark',
                                'log' => 'Log (Development)',
                                'array' => 'Array (Testing)',
                            ])
                            ->default('smtp')
                            ->live(),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('mail_host')
                                    ->label('SMTP Host')
                                    ->placeholder('smtp.gmail.com')
                                    ->default('smtp.gmail.com'),
                                    
                                Forms\Components\TextInput::make('mail_port')
                                    ->label('SMTP Port')
                                    ->numeric()
                                    ->placeholder('587')
                                    ->default(587),
                                    
                                Forms\Components\Select::make('mail_encryption')
                                    ->label('Encryption')
                                    ->options([
                                        'tls' => 'TLS',
                                        'ssl' => 'SSL',
                                        '' => 'None',
                                    ])
                                    ->default('tls'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('mail_mailer') === 'smtp'),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('mail_username')
                                    ->label('SMTP Username')
                                    ->placeholder('your-email@gmail.com')
                                    ->default('noreply@example.com'),
                                    
                                Forms\Components\TextInput::make('mail_password')
                                    ->label('SMTP Password')
                                    ->password()
                                    ->revealable()
                                    ->default('password123'),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('mail_mailer') === 'smtp'),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('mail_from_address')
                                    ->label('From Email Address')
                                    ->email()
                                    ->default('noreply@yoursite.com')
                                    ->placeholder('noreply@yoursite.com'),
                                    
                                Forms\Components\TextInput::make('mail_from_name')
                                    ->label('From Name')
                                    ->default('Your Site Name')
                                    ->placeholder('Your Site Name'),
                            ])
                            ->columns(2),
                        
                        // Email Testing
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('test_email')
                                    ->label('Test Email Address')
                                    ->email()
                                    ->placeholder('test@example.com')
                                    ->helperText('Send a test email to verify configuration'),
                                    
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('send_test_email')
                                        ->label('Send Test Email')
                                        ->icon('heroicon-m-paper-airplane')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->modalHeading('Send Test Email')
                                        ->modalDescription('Send a test email to verify your configuration?')
                                        ->modalSubmitActionLabel('Send Test')
                                        ->action(function (array $data) {
                                            $this->sendTestEmail($data['test_email'] ?? null);
                                        }),
                                ])->verticallyAlignCenter(),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),
                
                // WhatsApp & SMS Configuration
                Forms\Components\Section::make('WhatsApp & SMS Configuration')
                    ->description('Configure Kudisms API for WhatsApp and SMS messaging')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        // Notification Toggles
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('whatsapp_notifications_enabled')
                                    ->label('WhatsApp Notifications')
                                    ->helperText('Enable WhatsApp messaging')
                                    ->inline(false),
                                    
                                Forms\Components\Toggle::make('email_notifications_enabled')
                                    ->label('Email Notifications')
                                    ->helperText('Enable email messaging')
                                    ->inline(false),
                                    
                                Forms\Components\Select::make('otp_delivery_method')
                                    ->label('OTP Delivery Method')
                                    ->options([
                                        'whatsapp' => 'WhatsApp Priority',
                                        'sms' => 'SMS Only',
                                    ])
                                    ->default('whatsapp'),
                                    
                                Forms\Components\Toggle::make('otp_sms_fallback_enabled')
                                    ->label('SMS Fallback')
                                    ->helperText('Use SMS if WhatsApp fails')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(4),
                        
                        // Kudisms API Settings
                        Forms\Components\Fieldset::make('Kudisms API Credentials')
                            ->schema([
                                Forms\Components\TextInput::make('kudisms_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->revealable()
                                    ->default('Fyzr0q46cDXo7KhlefY1uHIC8SMiRnQZpdPbBmE2O5WJsjLawANtV9vkUTgG3x')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                    
                                Forms\Components\TextInput::make('kudisms_sender_id')
                                    ->label('Sender ID')
                                    ->default('Yapa')
                                    ->maxLength(50),
                            ])
                            ->columns(3),
                        
                        // WhatsApp Settings
                        Forms\Components\Fieldset::make('WhatsApp Settings')
                            ->schema([
                                Forms\Components\TextInput::make('kudisms_whatsapp_template_code')
                                    ->label('Template Code')
                                    ->default('default_template')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('kudisms_whatsapp_url')
                                    ->label('API Endpoint')
                                    ->url()
                                    ->default('https://my.kudisms.net/api/whatsapp')
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                        
                        // SMS Settings
                        Forms\Components\Fieldset::make('SMS Settings')
                            ->schema([
                                Forms\Components\TextInput::make('kudisms_sms_template_code')
                                    ->label('Template Code')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('kudisms_app_name_code')
                                    ->label('App Name Code')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('kudisms_sms_url')
                                    ->label('API Endpoint')
                                    ->url()
                                    ->default('https://my.kudisms.net/api/otp')
                                    ->maxLength(255),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_communication')
                        ->label('Save Communication Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveCommunicationSettings')
                        ->keyBindings(['mod+shift+c']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getPaymentTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Payments & API')
            ->icon('heroicon-o-credit-card')
            ->badge('Commerce')
            ->badgeColor('success')
            ->schema([
                // Paystack Configuration
                Forms\Components\Section::make('Paystack Payment Gateway')
                    ->description('Configure Paystack payment processing')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Toggle::make('paystack_enabled')
                            ->label('Enable Paystack')
                            ->helperText('Enable/disable payment processing')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('paystack_environment')
                                    ->label('Environment')
                                    ->options([
                                        'test' => 'Test/Sandbox',
                                        'live' => 'Live/Production',
                                    ])
                                    ->default('test'),
                                    
                                Forms\Components\TextInput::make('paystack_public_key')
                                    ->label('Public Key')
                                    ->placeholder('pk_test_...')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('paystack_secret_key')
                                    ->label('Secret Key')
                                    ->placeholder('sk_test_...')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('paystack_webhook_secret')
                                    ->label('Webhook Secret')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255)
                                    ->helperText('For webhook signature verification'),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('paystack_enabled')),
                    ])
                    ->collapsible(),
                
                // Payment Settings
                Forms\Components\Section::make('Payment Configuration')
                    ->description('Configure pricing and limits')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('credit_price_naira')
                                    ->label('Price per Credit')
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->default(3.00)
                                    ->prefix('₦'),
                                    
                                Forms\Components\TextInput::make('minimum_credits_purchase')
                                    ->label('Min Credits')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(100)
                                    ->suffix('credits'),
                                    
                                Forms\Components\TextInput::make('minimum_amount_naira')
                                    ->label('Min Amount')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(300)
                                    ->prefix('₦'),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
                
                // Airtime API Configuration
                Forms\Components\Section::make('Airtime API Configuration')
                    ->description('Configure Wazobianet airtime service')
                    ->icon('heroicon-o-signal')
                    ->schema([
                        Forms\Components\Toggle::make('airtime_api_enabled')
                            ->label('Enable Airtime API')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('airtime_api_token')
                                    ->label('API Token')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('airtime_api_url')
                                    ->label('API Base URL')
                                    ->url()
                                    ->default('https://wazobianet.com/api')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('airtime_api_enabled')),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('airtime_minimum_amount')
                                    ->label('Minimum Amount')
                                    ->numeric()
                                    ->default(100)
                                    ->minValue(50)
                                    ->suffix('NGN'),
                                    
                                Forms\Components\TextInput::make('airtime_maximum_amount')
                                    ->label('Maximum Amount')
                                    ->numeric()
                                    ->default(10000)
                                    ->minValue(1000)
                                    ->suffix('NGN'),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('airtime_api_enabled')),
                        
                        // Network Configuration
                        Forms\Components\Repeater::make('airtime_networks')
                            ->label('Supported Networks')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Network')
                                    ->maxLength(50),
                                    
                                Forms\Components\TextInput::make('network_id')
                                    ->label('ID')
                                    ->numeric(),
                                    
                                Forms\Components\TextInput::make('prefix')
                                    ->label('Prefix')
                                    ->maxLength(10)
                                    ->helperText('For auto-detection'),
                                    
                                Forms\Components\Toggle::make('enabled')
                                    ->label('Active')
                                    ->default(true),
                            ])
                            ->columns(4)
                            ->defaultItems(4)
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Network')
                            ->visible(fn (Forms\Get $get) => $get('airtime_api_enabled')),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_payment')
                        ->label('Save Payment Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('savePaymentSettings')
                        ->keyBindings(['mod+shift+p']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getFeaturesTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Features')
            ->icon('heroicon-o-puzzle-piece')
            ->schema([
                // Batch Configuration
                Forms\Components\Section::make('Batch Management')
                    ->description('Configure batch creation and matching settings')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('batch_auto_close_days')
                                    ->label('Auto-close After')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(365)
                                    ->suffix('days'),
                                    
                                Forms\Components\TextInput::make('trial_batch_limit')
                                    ->label('Trial Batch Size')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(1000)
                                    ->suffix('members'),
                                    
                                Forms\Components\TextInput::make('regular_batch_limit')
                                    ->label('Regular Batch Size')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(1000)
                                    ->suffix('members'),
                            ])
                            ->columns(3),
                        
                        Forms\Components\Fieldset::make('Matching Algorithm Weights')
                            ->schema([
                                Forms\Components\TextInput::make('location_weight')
                                    ->label('Location Weight')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),
                                    
                                Forms\Components\TextInput::make('interests_weight')
                                    ->label('Interests Weight')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),
                
                // Ad Settings
                Forms\Components\Section::make('Advertisement System')
                    ->description('Configure Share & Earn ad features')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\Toggle::make('ads_feature_enabled')
                            ->label('Enable Ad System')
                            ->helperText('Enable/disable Share & Earn feature')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('ad_earnings_per_view')
                                    ->label('Earnings per View')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->prefix('₦'),
                                    
                                Forms\Components\TextInput::make('ad_screenshot_wait_hours')
                                    ->label('Screenshot Wait Time')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(168)
                                    ->suffix('hours'),
                                    
                                Forms\Components\TextInput::make('max_ad_rejection_count')
                                    ->label('Max Rejections')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->helperText('Before suspension'),
                                    
                                Forms\Components\TextInput::make('appeal_cooldown_days')
                                    ->label('Appeal Cooldown')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(30)
                                    ->suffix('days'),
                            ])
                            ->columns(4)
                            ->visible(fn (Forms\Get $get) => $get('ads_feature_enabled')),
                    ])
                    ->collapsible(),
                
                // File Upload Settings
                Forms\Components\Section::make('File Upload Configuration')
                    ->description('Configure file upload limits and formats')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('max_file_upload_size')
                                    ->label('Max Upload Size')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix('MB'),
                                    
                                Forms\Components\TextInput::make('supported_image_formats')
                                    ->label('Image Formats')
                                    ->placeholder('jpg,jpeg,png,gif,webp')
                                    ->helperText('Comma-separated'),
                                    
                                Forms\Components\Toggle::make('vcf_export_enabled')
                                    ->label('VCF Export')
                                    ->helperText('Allow contact exports')
                                    ->inline(false),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_features')
                        ->label('Save Feature Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveFeatureSettings')
                        ->keyBindings(['mod+shift+f']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getMarketingTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('SEO & Marketing')
            ->icon('heroicon-o-chart-bar')
            ->schema([
                // SEO Settings
                Forms\Components\Section::make('Search Engine Optimization')
                    ->description('Configure SEO meta tags and search visibility')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->maxLength(60)
                            ->helperText('Optimal: 50-60 characters')
                            ->suffix(fn ($state) => (60 - strlen($state ?? '')) . ' left'),
                            
                        Forms\Components\Textarea::make('seo_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Optimal: 150-160 characters'),
                            
                        Forms\Components\TagsInput::make('seo_keywords')
                            ->label('Keywords')
                            ->separator(',')
                            ->placeholder('Add keyword')
                            ->helperText('Focus on 5-10 relevant keywords'),
                    ])
                    ->collapsible(),
                
                // Social Media Settings
                Forms\Components\Section::make('Social Media Integration')
                    ->description('Configure social sharing and OpenGraph')
                    ->icon('heroicon-o-share')
                    ->schema([
                        // OpenGraph
                        Forms\Components\Fieldset::make('OpenGraph Settings')
                            ->schema([
                                Forms\Components\TextInput::make('og_title')
                                    ->label('OG Title')
                                    ->maxLength(95)
                                    ->helperText('For social sharing'),
                                    
                                Forms\Components\Textarea::make('og_description')
                                    ->label('OG Description')
                                    ->rows(2)
                                    ->maxLength(200),
                                    
                                Forms\Components\Select::make('og_type')
                                    ->label('OG Type')
                                    ->options([
                                        'website' => 'Website',
                                        'article' => 'Article',
                                        'product' => 'Product',
                                    ])
                                    ->default('website'),
                                    
                                Forms\Components\FileUpload::make('og_image')
                                    ->label('OG Image')
                                    ->image()
                                    ->directory('seo')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg'])
                                    ->maxSize(2048)
                                    ->helperText('1200x630px recommended'),
                            ])
                            ->columns(2),
                        
                        // Twitter Card
                        Forms\Components\Fieldset::make('Twitter Card Settings')
                            ->schema([
                                Forms\Components\Select::make('twitter_card')
                                    ->label('Card Type')
                                    ->options([
                                        'summary' => 'Summary',
                                        'summary_large_image' => 'Large Image',
                                        'app' => 'App',
                                        'player' => 'Player',
                                    ])
                                    ->default('summary_large_image'),
                                    
                                Forms\Components\TextInput::make('twitter_site')
                                    ->label('Site Handle')
                                    ->prefix('@')
                                    ->placeholder('yoursite'),
                                    
                                Forms\Components\TextInput::make('twitter_creator')
                                    ->label('Creator Handle')
                                    ->prefix('@')
                                    ->placeholder('creator'),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
                
                // Banner Settings (Simplified)
                Forms\Components\Section::make('Banner Configuration')
                    ->description('Configure promotional banners')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\Toggle::make('banner_enabled')
                            ->label('Enable Banners')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Tabs::make('Banner Types')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Guest Banner')
                                    ->schema([
                                        Forms\Components\TextInput::make('banner_guest_title')
                                            ->label('Title'),
                                        Forms\Components\Textarea::make('banner_guest_description')
                                            ->label('Description')
                                            ->rows(2),
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('banner_guest_button_text')
                                                    ->label('Primary Button'),
                                                Forms\Components\TextInput::make('banner_guest_button_url')
                                                    ->label('Primary URL')
                                                    ->url(),
                                            ])
                                            ->columns(2),
                                    ]),
                                Forms\Components\Tabs\Tab::make('User Banner')
                                    ->schema([
                                        Forms\Components\TextInput::make('banner_auth_title')
                                            ->label('Title'),
                                        Forms\Components\Textarea::make('banner_auth_description')
                                            ->label('Description')
                                            ->rows(2),
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('banner_auth_button_text')
                                                    ->label('Button Text'),
                                                Forms\Components\TextInput::make('banner_auth_button_url')
                                                    ->label('Button URL')
                                                    ->url(),
                                            ])
                                            ->columns(2),
                                    ]),
                            ])
                            ->contained(false)
                            ->visible(fn (Forms\Get $get) => $get('banner_enabled')),
                    ])
                    ->collapsible()
                    ->collapsed(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_marketing')
                        ->label('Save Marketing Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveMarketingSettings')
                        ->keyBindings(['mod+shift+k']),
                ])->fullWidth(),
            ]);
    }
    
    protected function getSystemTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('System')
            ->icon('heroicon-o-server-stack')
            ->badge('Advanced')
            ->badgeColor('danger')
            ->schema([
                // Google OAuth
                Forms\Components\Section::make('Google OAuth Configuration')
                    ->description('Configure Google authentication')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\Toggle::make('google_oauth_enabled')
                            ->label('Enable Google OAuth')
                            ->live(),
                        
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('google_client_id')
                                    ->label('Client ID')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('google_client_secret')
                                    ->label('Client Secret')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('google_redirect_uri')
                                    ->label('Redirect URI')
                                    ->url()
                                    ->disabled()
                                    ->default(fn () => url('/auth/google/callback')),
                                    
                                Forms\Components\TagsInput::make('google_scopes')
                                    ->label('OAuth Scopes')
                                    ->default(['openid', 'profile', 'email'])
                                    ->suggestions([
                                        'openid',
                                        'profile',
                                        'email',
                                    ]),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('google_oauth_enabled')),
                        
                        Forms\Components\Placeholder::make('oauth_instructions')
                            ->content(new HtmlString('
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <h4 class="font-semibold mb-2">Setup Instructions:</h4>
                                    <ol class="list-decimal list-inside space-y-1 text-sm">
                                        <li>Visit <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                                        <li>Create OAuth 2.0 credentials</li>
                                        <li>Add redirect URI: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">' . url('/auth/google/callback') . '</code></li>
                                        <li>Copy credentials above</li>
                                    </ol>
                                </div>
                            '))
                            ->visible(fn (Forms\Get $get) => $get('google_oauth_enabled')),
                    ])
                    ->collapsible(),
                
                // Cache Management
                Forms\Components\Section::make('Cache Management')
                    ->description('Clear various application caches')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('clear_all_cache')
                                ->label('Clear All Cache')
                                ->icon('heroicon-m-trash')
                                ->color('danger')
                                ->size('lg')
                                ->requiresConfirmation()
                                ->modalHeading('Clear All Application Cache?')
                                ->modalDescription('This will clear all cached data including views, routes, and configuration.')
                                ->action(fn () => $this->clearCache()),
                            
                            Forms\Components\Actions\Action::make('clear_view_cache')
                                ->label('Views')
                                ->icon('heroicon-m-eye-slash')
                                ->color('gray')
                                ->action(fn () => $this->clearViewCache()),
                            
                            Forms\Components\Actions\Action::make('clear_route_cache')
                                ->label('Routes')
                                ->icon('heroicon-m-map')
                                ->color('gray')
                                ->action(fn () => $this->clearRouteCache()),
                            
                            Forms\Components\Actions\Action::make('clear_config_cache')
                                ->label('Config')
                                ->icon('heroicon-m-cog-6-tooth')
                                ->color('gray')
                                ->action(fn () => $this->clearConfigCache()),
                        ])
                        ->fullWidth(),
                    ])
                    ->collapsible(),
                
                // Scheduled Tasks
                Forms\Components\Section::make('Scheduled Tasks')
                    ->description('Manage cron jobs and scheduled commands')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Placeholder::make('cron_setup')
                            ->content(new HtmlString('
                                <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded font-mono text-sm">
                                    * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
                                </div>
                            ')),
                        
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('run_scheduler')
                                ->label('Run Scheduler')
                                ->icon('heroicon-m-play')
                                ->color('success')
                                ->requiresConfirmation()
                                ->action(fn () => $this->runScheduler()),
                            
                            Forms\Components\Actions\Action::make('list_tasks')
                                ->label('List Tasks')
                                ->icon('heroicon-m-list-bullet')
                                ->color('info')
                                ->action(fn () => $this->listScheduledTasks()),
                        ])->fullWidth(),
                        
                        Forms\Components\Fieldset::make('Manual Commands')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('expire_campaigns')
                                        ->label('Expire Campaigns')
                                        ->icon('heroicon-m-x-circle')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->action(fn () => $this->runCommand('ads:expire-campaigns')),
                                    
                                    Forms\Components\Actions\Action::make('cleanup_batches')
                                        ->label('Cleanup Batches')
                                        ->icon('heroicon-m-archive-box-x-mark')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->action(fn () => $this->runCommand('batches:cleanup-trials')),
                                    
                                    Forms\Components\Actions\Action::make('generate_avatars')
                                        ->label('Generate Avatars')
                                        ->icon('heroicon-m-user-circle')
                                        ->color('info')
                                        ->action(fn () => $this->runCommand('avatars:generate')),
                                ])->fullWidth(),
                            ]),
                    ])
                    ->collapsible(),
                    
                // Tab-specific save button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save_system')
                        ->label('Save System Settings')
                        ->icon('heroicon-m-check')
                        ->color('primary')
                        ->action('saveSystemSettings')
                        ->keyBindings(['mod+shift+s']),
                ])->fullWidth(),
            ]);
    }

    // =============================
    // Form Actions
    // =============================
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->icon('heroicon-m-check')
                ->action('save')
                ->keyBindings(['mod+s'])
                ->color('warning')
                ->outlined(),
                
            Action::make('saveAndClearCache')
                ->label('Save All & Clear Cache')
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->action(function () {
                    $this->save();
                    $this->clearCache();
                })
                ->outlined(),
                
            Action::make('reset')
                ->label('Reset to Defaults')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset All Settings?')
                ->modalDescription('This will reset all settings to their default values. This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, Reset Everything')
                ->action('resetToDefaults')
                ->outlined(),
        ];
    }

    // =============================
    // Action Methods
    // =============================
    
    public function save(): void
    {
        try {
            $this->form->validate();
            
            if (!$this->settingService) {
                $this->settingService = app(SettingService::class);
            }
            
            $data = $this->form->getState();
            $savedCount = 0;
            
            DB::beginTransaction();
            
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $type = $this->getSettingType($key, $value);
                    if ($this->settingService->set($key, $value, $type)) {
                        $savedCount++;
                    }
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Settings Saved')
                ->body("Successfully saved {$savedCount} settings")
                ->success()
                ->duration(3000)
                ->send();
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Save Failed')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
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
                ->title('Settings Reset')
                ->body('All settings have been reset to default values')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Reset Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // =============================
    // Individual Tab Save Methods
    // =============================
    
    public function saveGeneralSettings(): void
    {
        $this->saveTabSettings('General', [
            'app_name', 'app_version', 'registration_enabled', 'email_verification_required',
            'registration_bonus_credits', 'admin_contact_name', 'admin_contact_number', 'admin_contact_email'
        ]);
    }
    
    public function saveBrandingSettings(): void
    {
        $this->saveTabSettings('Branding', [
            'site_name', 'site_logo_name', 'site_logo', 'site_favicon',
            'brand_primary_color', 'brand_secondary_color'
        ]);
    }
    
    public function saveMaintenanceSettings(): void
    {
        $this->saveTabSettings('Maintenance', [
            'maintenance_mode', 'maintenance_message', 'maintenance_end_time', 'maintenance_allowed_ips'
        ]);
    }
    
    public function saveCommunicationSettings(): void
    {
        $this->saveTabSettings('Communication', [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username',
            'mail_password', 'mail_from_address', 'mail_from_name', 'kudisms_api_token',
            'kudisms_whatsapp_template_code', 'kudisms_whatsapp_url', 'kudisms_sms_template_code',
            'kudisms_app_name_code', 'kudisms_sms_url'
        ]);
    }
    
    public function savePaymentSettings(): void
    {
        $this->saveTabSettings('Payment', [
            'paystack_enabled', 'paystack_environment', 'paystack_public_key', 'paystack_secret_key',
            'paystack_webhook_secret', 'credit_price_per_unit', 'minimum_credit_purchase',
            'maximum_credit_purchase', 'airtime_api_enabled', 'airtime_api_token', 'airtime_api_url',
            'airtime_minimum_amount', 'airtime_maximum_amount', 'airtime_networks'
        ]);
    }
    
    public function saveFeatureSettings(): void
    {
        $this->saveTabSettings('Features', [
            'batch_auto_close_days', 'trial_batch_limit', 'regular_batch_limit', 'location_weight',
            'interests_weight', 'ads_feature_enabled', 'ad_earnings_per_view', 'ad_minimum_views',
            'ad_maximum_duration_days', 'max_file_upload_size', 'supported_image_formats', 'vcf_export_enabled'
        ]);
    }
    
    public function saveMarketingSettings(): void
    {
        $this->saveTabSettings('Marketing', [
            'seo_title', 'seo_description', 'seo_keywords', 'og_title', 'og_description',
            'og_type', 'og_image', 'twitter_card', 'twitter_site', 'twitter_creator',
            'analytics_enabled', 'google_analytics_id', 'facebook_pixel_id', 'banner_enabled',
            'banner_guest_title', 'banner_guest_description', 'banner_guest_button_text',
            'banner_guest_button_url', 'banner_auth_title', 'banner_auth_description',
            'banner_auth_button_text', 'banner_auth_button_url'
        ]);
    }
    
    public function saveSystemSettings(): void
    {
        $this->saveTabSettings('System', [
            'google_oauth_enabled', 'google_client_id', 'google_client_secret',
            'google_redirect_uri', 'google_scopes'
        ]);
    }
    
    private function saveTabSettings(string $tabName, array $fields): void
    {
        try {
            $this->form->validate();
            
            if (!$this->settingService) {
                $this->settingService = app(SettingService::class);
            }
            
            $data = $this->form->getState();
            $savedCount = 0;
            
            DB::beginTransaction();
            
            foreach ($fields as $field) {
                if (array_key_exists($field, $data) && $data[$field] !== null) {
                    $type = $this->getSettingType($field, $data[$field]);
                    if ($this->settingService->set($field, $data[$field], $type)) {
                        $savedCount++;
                    }
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title($tabName . ' Settings Saved')
                ->body("Successfully saved {$savedCount} {$tabName} settings")
                ->success()
                ->duration(3000)
                ->send();
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title($tabName . ' Save Failed')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    // =============================
    // Helper Methods
    // =============================
    
    protected function getSettingsData(): array
    {
        if (!$this->settingService) {
            $this->settingService = app(SettingService::class);
        }
        
        return $this->settingService->all();
    }

    protected function getSettingType(string $key, $value): string
    {
        return match(true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string',
        };
    }

    // =============================
    // Cache Management Methods
    // =============================
    
    public function clearCache(): void
    {
        try {
            $commands = [
                'cache:clear' => 'Application cache',
                'view:clear' => 'View cache',
                'route:clear' => 'Route cache',
                'config:clear' => 'Config cache',
            ];
            
            foreach ($commands as $command => $description) {
                Artisan::call($command);
            }
            
            Notification::make()
                ->title('Cache Cleared')
                ->body('All application caches have been cleared successfully')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Cache Clear Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function clearViewCache(): void
    {
        $this->runArtisanCommand('view:clear', 'View cache');
    }
    
    public function clearRouteCache(): void
    {
        $this->runArtisanCommand('route:clear', 'Route cache');
    }
    
    public function clearConfigCache(): void
    {
        $this->runArtisanCommand('config:clear', 'Config cache');
    }

    // =============================
    // Email Testing
    // =============================
    
    public function sendTestEmail(?string $email = null): void
    {
        $formData = $this->form->getState();
        $testEmail = $email ?? $formData['test_email'] ?? null;
        
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('Invalid Email')
                ->body('Please enter a valid email address')
                ->danger()
                ->send();
            return;
        }
        
        try {
            $this->updateMailConfig($formData);
            
            Mail::raw(
                'This is a test email from ' . config('app.name') . '. Your email configuration is working correctly!',
                function ($message) use ($testEmail) {
                    $message->to($testEmail)
                        ->subject('Test Email - ' . config('app.name'));
                }
            );
            
            Notification::make()
                ->title('Test Email Sent')
                ->body("Test email sent to {$testEmail}")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email Send Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    private function updateMailConfig(array $data): void
    {
        $mappings = [
            'mail_mailer' => 'mail.default',
            'mail_host' => 'mail.mailers.smtp.host',
            'mail_port' => 'mail.mailers.smtp.port',
            'mail_username' => 'mail.mailers.smtp.username',
            'mail_password' => 'mail.mailers.smtp.password',
            'mail_encryption' => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name' => 'mail.from.name',
        ];
        
        foreach ($mappings as $formField => $configKey) {
            if (isset($data[$formField])) {
                config([$configKey => $data[$formField]]);
            }
        }
    }

    // =============================
    // Scheduled Tasks
    // =============================
    
    public function runScheduler(): void
    {
        $this->runArtisanCommand('schedule:run', 'Scheduler');
    }
    
    public function listScheduledTasks(): void
    {
        $this->runArtisanCommand('schedule:list', 'Task list');
    }
    
    public function runCommand(string $command): void
    {
        $this->runArtisanCommand($command, "Command '{$command}'");
    }
    
    private function runArtisanCommand(string $command, string $description): void
    {
        try {
            Artisan::call($command);
            
            Notification::make()
                ->title('Command Executed')
                ->body("{$description} executed successfully")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Command Failed')
                ->body("Failed to execute {$description}: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }
}