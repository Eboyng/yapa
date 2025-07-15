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
use Illuminate\Support\Facades\Mail;

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
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('app_version')
                                            ->label('Application Version')
                                            ->maxLength(50),
                                        Forms\Components\Toggle::make('registration_enabled')
                                            ->label('Registration Enabled')
                                            ->helperText('Allow new user registrations'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Contact Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('admin_contact_name')
                                            ->label('Admin Contact Name')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('admin_contact_number')
                                            ->label('Admin Contact Number')
                                            ->tel()
                                            ->maxLength(20),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Site Branding')
                            ->schema([
                                Forms\Components\Section::make('Site Identity')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Site Name')
                                            ->maxLength(255)
                                            ->helperText('The name displayed in the browser title and throughout the site'),
                                        Forms\Components\TextInput::make('site_logo_name')
                                            ->label('Logo Text')
                                            ->maxLength(255)
                                            ->helperText('Text to display alongside or instead of the logo'),
                                        Forms\Components\FileUpload::make('site_logo')
                                            ->label('Site Logo')
                                            ->image()
                                            ->directory('branding')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                                            ->maxSize(2048)
                                            ->helperText('Upload your site logo (PNG, JPG, SVG - Max 2MB)'),
                                        Forms\Components\FileUpload::make('site_favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('branding')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                            ->maxSize(512)
                                            ->helperText('Upload your favicon (ICO, PNG - Max 512KB)'),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Maintenance Mode')
                            ->schema([
                                Forms\Components\Section::make('Maintenance Settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('maintenance_mode')
                                            ->label('Enable Maintenance Mode')
                                            ->helperText('Put the site in maintenance mode for all users except allowed IPs')
                                            ->live(),
                                        Forms\Components\Textarea::make('maintenance_message')
                                            ->label('Maintenance Message')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Message to display on the maintenance page')
                                            ->visible(fn (callable $get) => $get('maintenance_mode')),
                                        Forms\Components\DateTimePicker::make('maintenance_end_time')
                                            ->label('Estimated End Time')
                                            ->helperText('Optional: Set when maintenance is expected to end (enables countdown)')
                                            ->visible(fn (callable $get) => $get('maintenance_mode')),
                                        Forms\Components\Textarea::make('maintenance_allowed_ips')
                                            ->label('Allowed IP Addresses')
                                            ->rows(3)
                                            ->helperText('Comma-separated list of IP addresses that can access the site during maintenance')
                                            ->placeholder('192.168.1.1, 203.0.113.1')
                                            ->visible(fn (callable $get) => $get('maintenance_mode')),
                                    ])
                                    ->columns(1),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('SEO & Social')
                            ->schema([
                                Forms\Components\Section::make('SEO Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title')
                                            ->label('SEO Title')
                                            ->maxLength(60)
                                            ->helperText('Title tag for search engines (max 60 characters)'),
                                        Forms\Components\Textarea::make('seo_description')
                                            ->label('SEO Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Meta description for search engines (max 160 characters)'),
                                        Forms\Components\TextInput::make('seo_keywords')
                                            ->label('SEO Keywords')
                                            ->helperText('Comma-separated keywords for search engines'),
                                    ])
                                    ->columns(1),
                                
                                Forms\Components\Section::make('OpenGraph Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('og_title')
                                            ->label('OpenGraph Title')
                                            ->maxLength(95)
                                            ->helperText('Title for social media sharing (max 95 characters)'),
                                        Forms\Components\Textarea::make('og_description')
                                            ->label('OpenGraph Description')
                                            ->rows(3)
                                            ->maxLength(200)
                                            ->helperText('Description for social media sharing (max 200 characters)'),
                                        Forms\Components\FileUpload::make('og_image')
                                            ->label('OpenGraph Image')
                                            ->image()
                                            ->directory('seo')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg'])
                                            ->maxSize(2048)
                                            ->helperText('Image for social media sharing (1200x630px recommended, max 2MB)'),
                                        Forms\Components\Select::make('og_type')
                                            ->label('OpenGraph Type')
                                            ->options([
                                                'website' => 'Website',
                                                'article' => 'Article',
                                                'product' => 'Product',
                                            ])
                                            ->default('website'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Twitter Card Settings')
                                    ->schema([
                                        Forms\Components\Select::make('twitter_card')
                                            ->label('Twitter Card Type')
                                            ->options([
                                                'summary' => 'Summary',
                                                'summary_large_image' => 'Summary Large Image',
                                                'app' => 'App',
                                                'player' => 'Player',
                                            ])
                                            ->default('summary_large_image'),
                                        Forms\Components\TextInput::make('twitter_site')
                                            ->label('Twitter Site Handle')
                                            ->prefix('@')
                                            ->helperText('Your site\'s Twitter handle'),
                                        Forms\Components\TextInput::make('twitter_creator')
                                            ->label('Twitter Creator Handle')
                                            ->prefix('@')
                                            ->helperText('Content creator\'s Twitter handle'),
                                    ])
                                    ->columns(3),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Batch Settings')
                            ->schema([
                                Forms\Components\Section::make('Batch Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('batch_auto_close_days')
                                            ->label('Auto Close Days')
                                            ->helperText('Number of days after which batches auto-close')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(365),
                                        Forms\Components\TextInput::make('trial_batch_limit')
                                            ->label('Trial Batch Limit')
                                            ->helperText('Maximum members in trial batches')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(1000),
                                        Forms\Components\TextInput::make('regular_batch_limit')
                                            ->label('Regular Batch Limit')
                                            ->helperText('Maximum members in regular batches')
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
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('interests_weight')
                                            ->label('Interests Weight (%)')
                                            ->helperText('Weight given to interests matching')
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
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('ad_screenshot_wait_hours')
                                            ->label('Screenshot Wait Hours')
                                            ->helperText('Hours to wait before requiring screenshot')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(168),
                                        Forms\Components\TextInput::make('max_ad_rejection_count')
                                            ->label('Max Rejection Count')
                                            ->helperText('Maximum rejections before account suspension')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(10),
                                        Forms\Components\TextInput::make('appeal_cooldown_days')
                                            ->label('Appeal Cooldown Days')
                                            ->helperText('Days to wait before allowing appeals')
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
                        
                        Forms\Components\Tabs\Tab::make('Payment Settings')
                            ->schema([
                                Forms\Components\Section::make('Paystack Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('paystack_public_key')
                                            ->label('Paystack Public Key')
                                            ->helperText('Your Paystack public key for frontend integration')
                                            ->maxLength(255)
                                            ->placeholder('pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                                        Forms\Components\TextInput::make('paystack_secret_key')
                                            ->label('Paystack Secret Key')
                                            ->helperText('Your Paystack secret key for backend API calls')
                                            ->password()
                                            ->revealable()
                                            ->maxLength(255)
                                            ->placeholder('sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                                        Forms\Components\TextInput::make('paystack_webhook_secret')
                                            ->label('Paystack Webhook Secret')
                                            ->helperText('Secret key for verifying webhook signatures')
                                            ->password()
                                            ->revealable()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('paystack_environment')
                                            ->label('Environment')
                                            ->options([
                                                'test' => 'Test/Sandbox',
                                                'live' => 'Live/Production',
                                            ])
                                            ->default('test'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Payment Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('credit_price_naira')
                                            ->label('Credit Price (NGN)')
                                            ->helperText('Price per credit in Nigerian Naira')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->default(3.00)
                                            ->prefix('₦'),
                                        Forms\Components\TextInput::make('minimum_credits_purchase')
                                            ->label('Minimum Credits Purchase')
                                            ->helperText('Minimum number of credits that can be purchased')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(100),
                                        Forms\Components\TextInput::make('minimum_amount_naira')
                                            ->label('Minimum Amount (NGN)')
                                            ->helperText('Minimum payment amount in Nigerian Naira')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(300)
                                            ->prefix('₦'),
                                        Forms\Components\Toggle::make('paystack_enabled')
                                            ->label('Enable Paystack Payments')
                                            ->helperText('Enable or disable Paystack payment processing')
                                            ->default(true),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('File Upload')
                            ->schema([
                                Forms\Components\Section::make('Upload Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_file_upload_size')
                                            ->label('Max File Upload Size (MB)')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->suffix('MB'),
                                        Forms\Components\TextInput::make('supported_image_formats')
                                            ->label('Supported Image Formats')
                                            ->helperText('Comma-separated list of supported formats')
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
                        
                        Forms\Components\Tabs\Tab::make('Google OAuth')
                            ->schema([
                                Forms\Components\Section::make('Google OAuth Configuration')
                                    ->schema([
                                        Forms\Components\Toggle::make('google_oauth_enabled')
                                            ->label('Enable Google OAuth')
                                            ->helperText('Enable Google OAuth authentication for users')
                                            ->live(),
                                        Forms\Components\TextInput::make('google_client_id')
                                            ->label('Google Client ID')
                                            ->placeholder('Enter your Google OAuth Client ID')
                                            ->maxLength(255)
                                            ->helperText('OAuth 2.0 Client ID from Google Cloud Console')
                                            ->visible(fn (callable $get) => $get('google_oauth_enabled')),
                                        Forms\Components\TextInput::make('google_client_secret')
                                            ->label('Google Client Secret')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('Enter your Google OAuth Client Secret')
                                            ->maxLength(255)
                                            ->helperText('OAuth 2.0 Client Secret from Google Cloud Console')
                                            ->visible(fn (callable $get) => $get('google_oauth_enabled')),
                                        Forms\Components\TextInput::make('google_redirect_uri')
                                            ->label('Redirect URI')
                                            ->url()
                                            ->placeholder('https://yoursite.com/auth/google/callback')
                                            ->maxLength(255)
                                            ->helperText('Must match the redirect URI configured in Google Cloud Console')
                                            ->visible(fn (callable $get) => $get('google_oauth_enabled')),
                                        Forms\Components\TagsInput::make('google_scopes')
                                            ->label('OAuth Scopes')
                                            ->placeholder('Add scope')
                                            ->helperText('Google OAuth scopes (e.g., openid, profile, email)')
                                            ->suggestions([
                                                'openid',
                                                'profile', 
                                                'email',
                                                'https://www.googleapis.com/auth/userinfo.profile',
                                                'https://www.googleapis.com/auth/userinfo.email'
                                            ])
                                            ->visible(fn (callable $get) => $get('google_oauth_enabled')),
                                    ])
                                    ->columns(2),
                                    
                                Forms\Components\Section::make('Setup Instructions')
                                    ->schema([
                                        Forms\Components\Placeholder::make('google_setup_instructions')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString('
                                                <div class="space-y-4">
                                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Google OAuth Setup Instructions:</h4>
                                                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                                                        <li>Create a new project or select an existing one</li>
                                                        <li>Enable the Google+ API and Google OAuth2 API</li>
                                                        <li>Go to "Credentials" and create "OAuth 2.0 Client IDs"</li>
                                                        <li>Set the authorized redirect URI to: <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">' . url('/auth/google/callback') . '</code></li>
                                                        <li>Copy the Client ID and Client Secret to the fields above</li>
                                                        <li>Save the settings and test the OAuth flow</li>
                                                    </ol>
                                                </div>
                                            '))
                                    ])
                                    ->columns(1)
                                    ->visible(fn (callable $get) => $get('google_oauth_enabled')),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('System Management')
                            ->schema([
                                Forms\Components\Section::make('Cache Management')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('clear_cache')
                                                ->label('Clear Application Cache')
                                                ->icon('heroicon-o-trash')
                                                ->color('warning')
                                                ->requiresConfirmation()
                                                ->modalHeading('Clear Application Cache')
                                                ->modalDescription('This will clear all cached data including views, routes, and configuration.')
                                                ->action(function () {
                                                    $this->clearCache();
                                                }),
                                            Forms\Components\Actions\Action::make('clear_view_cache')
                                                ->label('Clear View Cache')
                                                ->icon('heroicon-o-eye-slash')
                                                ->color('info')
                                                ->action(function () {
                                                    $this->clearViewCache();
                                                }),
                                            Forms\Components\Actions\Action::make('clear_route_cache')
                                                ->label('Clear Route Cache')
                                                ->icon('heroicon-o-map')
                                                ->color('info')
                                                ->action(function () {
                                                    $this->clearRouteCache();
                                                }),
                                            Forms\Components\Actions\Action::make('clear_config_cache')
                                                ->label('Clear Config Cache')
                                                ->icon('heroicon-o-cog-6-tooth')
                                                ->color('info')
                                                ->action(function () {
                                                    $this->clearConfigCache();
                                                }),
                                        ])
                                    ])
                                    ->columns(1),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Email Configuration')
                            ->schema([
                                Forms\Components\Section::make('Email Settings')
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
                                            ->default('log')
                                            ->live(),
                                        Forms\Components\TextInput::make('mail_host')
                                            ->label('SMTP Host')
                                            ->placeholder('smtp.gmail.com')
                                            ->visible(fn (callable $get) => $get('mail_mailer') === 'smtp'),
                                        Forms\Components\TextInput::make('mail_port')
                                            ->label('SMTP Port')
                                            ->numeric()
                                            ->placeholder('587')
                                            ->visible(fn (callable $get) => $get('mail_mailer') === 'smtp'),
                                        Forms\Components\TextInput::make('mail_username')
                                            ->label('SMTP Username')
                                            ->placeholder('your-email@gmail.com')
                                            ->visible(fn (callable $get) => $get('mail_mailer') === 'smtp'),
                                        Forms\Components\TextInput::make('mail_password')
                                            ->label('SMTP Password')
                                            ->password()
                                            ->revealable()
                                            ->visible(fn (callable $get) => $get('mail_mailer') === 'smtp'),
                                        Forms\Components\Select::make('mail_encryption')
                                            ->label('Encryption')
                                            ->options([
                                                'tls' => 'TLS',
                                                'ssl' => 'SSL',
                                                null => 'None',
                                            ])
                                            ->default('tls')
                                            ->visible(fn (callable $get) => $get('mail_mailer') === 'smtp'),
                                        Forms\Components\TextInput::make('mail_from_address')
                                            ->label('From Email Address')
                                            ->email()
                                            ->placeholder('noreply@yoursite.com'),
                                        Forms\Components\TextInput::make('mail_from_name')
                                            ->label('From Name')
                                            ->placeholder('Your Site Name'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Section::make('Email Testing')
                                    ->schema([
                                        Forms\Components\TextInput::make('test_email')
                                            ->label('Test Email Address')
                                            ->email()
                                            ->placeholder('test@example.com')
                                            ->helperText('Enter an email address to send a test email'),
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('send_test_email')
                                                ->label('Send Test Email')
                                                ->icon('heroicon-o-envelope')
                                                ->color('success')
                                                ->requiresConfirmation()
                                                ->modalHeading('Send Test Email')
                                                ->modalDescription('This will send a test email to verify your email configuration.')
                                                ->action(function (array $data) {
                                                    $this->sendTestEmail($data['test_email'] ?? null);
                                                }),
                                        ])
                                    ])
                                    ->columns(1),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('API Settings')
                            ->schema([
                                Forms\Components\Section::make('Airtime API Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('airtime_api_token')
                                            ->label('Airtime API Token')
                                            ->password()
                                            ->revealable()
                                            ->maxLength(255)
                                            ->helperText('API token for Wazobianet airtime service'),
                                        Forms\Components\TextInput::make('airtime_api_url')
                                            ->label('Airtime API Base URL')
                                            ->url()
                                            ->default('https://wazobianet.com/api')
                                            ->maxLength(255)
                                            ->helperText('Base URL for the airtime API service'),
                                        Forms\Components\Toggle::make('airtime_api_enabled')
                                            ->label('Enable Airtime API')
                                            ->default(true)
                                            ->helperText('Enable or disable airtime withdrawal functionality'),
                                        Forms\Components\TextInput::make('airtime_minimum_amount')
                                            ->label('Minimum Airtime Amount')
                                            ->numeric()
                                            ->default(100)
                                            ->minValue(50)
                                            ->maxValue(5000)
                                            ->suffix('NGN')
                                            ->helperText('Minimum amount for airtime withdrawal'),
                                        Forms\Components\TextInput::make('airtime_maximum_amount')
                                            ->label('Maximum Airtime Amount')
                                            ->numeric()
                                            ->default(10000)
                                            ->minValue(1000)
                                            ->maxValue(50000)
                                            ->suffix('NGN')
                                            ->helperText('Maximum amount for airtime withdrawal'),
                                    ])
                                    ->columns(2),
                                    
                                Forms\Components\Section::make('Network Configuration')
                                    ->schema([
                                        Forms\Components\Repeater::make('airtime_networks')
                                            ->label('Supported Networks')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Network Name')
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('network_id')
                                                    ->label('Network ID')
                                                    ->numeric()
                                                    ->helperText('API network ID'),
                                                Forms\Components\TextInput::make('prefix')
                                                    ->label('Phone Prefix')
                                                    ->maxLength(10)
                                                    ->helperText('Phone number prefix for auto-detection'),
                                                Forms\Components\Toggle::make('enabled')
                                                    ->label('Enabled')
                                                    ->default(true),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ])
                                    ->columns(1),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Scheduled Tasks')
                            ->schema([
                                Forms\Components\Section::make('Task Management')
                                    ->schema([
                                        Forms\Components\Placeholder::make('cron_info')
                                            ->label('Cron Job Setup')
                                            ->content('To enable scheduled tasks, add this cron job to your server:\n\n* * * * * cd/path/to/your/project && php artisan schedule:run >> /dev/null 2>&1')
                                            ->helperText('Replace "/path/to/your/project" with the actual path to your Laravel application'),
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('run_scheduler')
                                                ->label('Run Scheduler Now')
                                                ->icon('heroicon-o-play')
                                                ->color('success')
                                                ->requiresConfirmation()
                                                ->modalHeading('Run Scheduled Tasks')
                                                ->modalDescription('This will manually run all scheduled tasks that are due.')
                                                ->action(function () {
                                                    $this->runScheduler();
                                                }),
                                            Forms\Components\Actions\Action::make('list_scheduled_tasks')
                                                ->label('View Scheduled Tasks')
                                                ->icon('heroicon-o-list-bullet')
                                                ->color('info')
                                                ->action(function () {
                                                    $this->listScheduledTasks();
                                                }),
                                        ])
                                    ])
                                    ->columns(1),
                                
                                Forms\Components\Section::make('Available Commands')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('expire_ad_campaigns')
                                                ->label('Expire Ad Campaigns')
                                                ->icon('heroicon-o-clock')
                                                ->color('warning')
                                                ->requiresConfirmation()
                                                ->action(function () {
                                                    $this->runCommand('ads:expire-campaigns');
                                                }),
                                            Forms\Components\Actions\Action::make('cleanup_trial_batches')
                                                ->label('Cleanup Trial Batches')
                                                ->icon('heroicon-o-trash')
                                                ->color('warning')
                                                ->requiresConfirmation()
                                                ->action(function () {
                                                    $this->runCommand('batches:cleanup-trials');
                                                }),
                                            Forms\Components\Actions\Action::make('generate_avatars')
                                                ->label('Generate User Avatars')
                                                ->icon('heroicon-o-user-circle')
                                                ->color('info')
                                                ->action(function () {
                                                    $this->runCommand('avatars:generate');
                                                }),
                                        ])
                                    ])
                                    ->columns(1),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Banner Settings')
                            ->schema([
                                Forms\Components\Section::make('Banner Control')
                                    ->schema([
                                        Forms\Components\Toggle::make('banner_enabled')
                                            ->label('Enable Banner')
                                            ->helperText('Show or hide the banner on the batch list page')
                                            ->default(true)
                                            ->live(),
                                        Forms\Components\Toggle::make('banner_auto_slide')
                                            ->label('Auto Slide')
                                            ->helperText('Enable automatic sliding between banner slides')
                                            ->default(true)
                                            ->visible(fn (callable $get) => $get('banner_enabled'))
                                            ->live(),
                                        Forms\Components\TextInput::make('banner_slide_interval')
                                            ->label('Slide Interval (seconds)')
                                            ->helperText('Time between automatic slides')
                                            ->numeric()
                                            ->minValue(3)
                                            ->maxValue(30)
                                            ->default(5)
                                            ->visible(fn (callable $get) => $get('banner_enabled') && $get('banner_auto_slide')),
                                    ])
                                    ->columns(3),
                                
                                Forms\Components\Section::make('Guest User Banner')
                                    ->schema([
                                        Forms\Components\TextInput::make('banner_guest_title')
                                            ->label('Title')
                                            ->maxLength(100)
                                            ->default('Welcome to Yapa')
                                            ->helperText('Main title for guest users'),
                                        Forms\Components\TextInput::make('banner_guest_subtitle')
                                            ->label('Subtitle')
                                            ->maxLength(150)
                                            ->default('Connect, Network, Grow Together')
                                            ->helperText('Subtitle for guest users'),
                                        Forms\Components\Textarea::make('banner_guest_description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->maxLength(300)
                                            ->default('Join our vibrant community and discover meaningful connections. Network with like-minded individuals and grow your professional circle.')
                                            ->helperText('Detailed description for guest users'),
                                        Forms\Components\TextInput::make('banner_guest_button_text')
                                            ->label('Primary Button Text')
                                            ->maxLength(50)
                                            ->default('Get Started')
                                            ->helperText('Text for the primary action button'),
                                        Forms\Components\TextInput::make('banner_guest_button_url')
                                            ->label('Primary Button URL')
                                            ->rules(['regex:/^(https?:\/\/|\/).*/'])
                                            ->default('/register')
                                            ->helperText('URL for the primary action button (can be relative like /register or absolute like https://example.com)'),
                                        Forms\Components\TextInput::make('banner_guest_secondary_button_text')
                                            ->label('Secondary Button Text')
                                            ->maxLength(50)
                                            ->default('Login')
                                            ->helperText('Text for the secondary action button'),
                                        Forms\Components\TextInput::make('banner_guest_secondary_button_url')
                                            ->label('Secondary Button URL')
                                            ->rules(['regex:/^(https?:\/\/|\/).*/'])
                                            ->default('/login')
                                            ->helperText('URL for the secondary action button (can be relative like /login or absolute like https://example.com)'),
                                        Forms\Components\FileUpload::make('banner_guest_background_image')
                                            ->label('Background Image')
                                            ->image()
                                            ->directory('banners')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/webp'])
                                            ->maxSize(2048)
                                            ->helperText('Background image for guest banner (optional)'),
                                        Forms\Components\Select::make('banner_guest_background_type')
                                            ->label('Background Type')
                                            ->options([
                                                'gradient' => 'Gradient',
                                                'image' => 'Image',
                                                'color' => 'Solid Color',
                                            ])
                                            ->default('gradient')
                                            ->helperText('Choose background style'),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (callable $get) => $get('banner_enabled')),
                                
                                Forms\Components\Section::make('Authenticated User Banner')
                                    ->schema([
                                        Forms\Components\TextInput::make('banner_auth_title')
                                            ->label('Title')
                                            ->maxLength(100)
                                            ->default('Join Our WhatsApp Community')
                                            ->helperText('Main title for authenticated users'),
                                        Forms\Components\TextInput::make('banner_auth_subtitle')
                                            ->label('Subtitle')
                                            ->maxLength(150)
                                            ->default('Stay Connected & Get Updates')
                                            ->helperText('Subtitle for authenticated users'),
                                        Forms\Components\Textarea::make('banner_auth_description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->maxLength(300)
                                            ->default('Join our WhatsApp community to receive instant updates, connect with other members, and never miss important announcements.')
                                            ->helperText('Detailed description for authenticated users'),
                                        Forms\Components\TextInput::make('banner_auth_button_text')
                                            ->label('Button Text')
                                            ->maxLength(50)
                                            ->default('Join WhatsApp Group')
                                            ->helperText('Text for the action button'),
                                        Forms\Components\TextInput::make('banner_auth_button_url')
                                            ->label('WhatsApp Group URL')
                                            ->url()
                                            ->default('https://chat.whatsapp.com/your-group-link')
                                            ->helperText('WhatsApp group invitation link'),
                                        Forms\Components\FileUpload::make('banner_auth_background_image')
                                            ->label('Background Image')
                                            ->image()
                                            ->directory('banners')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/webp'])
                                            ->maxSize(2048)
                                            ->helperText('Background image for auth banner (optional)'),
                                        Forms\Components\Select::make('banner_auth_background_type')
                                            ->label('Background Type')
                                            ->options([
                                                'gradient' => 'Gradient',
                                                'image' => 'Image',
                                                'color' => 'Solid Color',
                                            ])
                                            ->default('gradient')
                                            ->helperText('Choose background style'),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (callable $get) => $get('banner_enabled')),
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
                ->action('save')
                ->requiresConfirmation(false),
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
            // Debug: Log that save method was called
            \Log::info('Settings save method called');
            
            // Validate the form first
            $this->form->validate();
            \Log::info('Form validation passed');
            
            if (!$this->settingService) {
                $this->settingService = app(SettingService::class);
            }
            
            $data = $this->form->getState();
            \Log::info('Form data retrieved', ['data_count' => count($data)]);
            
            // Test database connection
            $dbTest = \DB::table('settings')->count();
            \Log::info('Database connection test', ['settings_count' => $dbTest]);
            
            $savedCount = 0;
            foreach ($data as $key => $value) {
                $type = $this->getSettingType($key, $value);
                $result = $this->settingService->set($key, $value, $type);
                if ($result) {
                    $savedCount++;
                }
                \Log::info('Setting saved', ['key' => $key, 'type' => $type, 'result' => $result]);
            }
            
            \Log::info('Settings save completed', ['total_saved' => $savedCount, 'total_data' => count($data)]);
            
            Notification::make()
                ->title('Settings saved successfully')
                ->body("Saved {$savedCount} out of " . count($data) . " settings")
                ->success()
                ->send();
                
        } catch (Halt $exception) {
            \Log::info('Settings save halted');
            return;
        } catch (\Exception $e) {
            \Log::error('Settings save failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Notification::make()
                ->title('Failed to save settings')
                ->body($e->getMessage())
                ->danger()
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
    
    // Cache Management Methods
    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            
            Notification::make()
                ->title('Cache Cleared')
                ->body('All application cache has been cleared successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to clear cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function clearViewCache(): void
    {
        try {
            Artisan::call('view:clear');
            
            Notification::make()
                ->title('View Cache Cleared')
                ->body('View cache has been cleared successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to clear view cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function clearRouteCache(): void
    {
        try {
            Artisan::call('route:clear');
            
            Notification::make()
                ->title('Route Cache Cleared')
                ->body('Route cache has been cleared successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to clear route cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function clearConfigCache(): void
    {
        try {
            Artisan::call('config:clear');
            
            Notification::make()
                ->title('Config Cache Cleared')
                ->body('Configuration cache has been cleared successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to clear config cache: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    // Email Management Methods
    public function sendTestEmail(?string $email = null): void
    {
        $formData = $this->form->getState();
        $testEmail = $email ?? $formData['test_email'] ?? null;
        
        if (!$testEmail) {
            Notification::make()
                ->title('Error')
                ->body('Please enter a valid email address.')
                ->danger()
                ->send();
            return;
        }
        
        try {
            // Temporarily update mail configuration with form data
            $this->updateMailConfig($formData);
            
            Mail::raw('This is a test email from your application. If you received this, your email configuration is working correctly!', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('Test Email - Configuration Verification');
            });
            
            Notification::make()
                ->title('Test Email Sent')
                ->body('Test email has been sent to ' . $testEmail)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email Error')
                ->body('Failed to send test email: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    private function updateMailConfig(array $data): void
    {
        if (isset($data['mail_mailer'])) {
            config(['mail.default' => $data['mail_mailer']]);
        }
        
        if (isset($data['mail_host'])) {
            config(['mail.mailers.smtp.host' => $data['mail_host']]);
        }
        
        if (isset($data['mail_port'])) {
            config(['mail.mailers.smtp.port' => $data['mail_port']]);
        }
        
        if (isset($data['mail_username'])) {
            config(['mail.mailers.smtp.username' => $data['mail_username']]);
        }
        
        if (isset($data['mail_password'])) {
            config(['mail.mailers.smtp.password' => $data['mail_password']]);
        }
        
        if (isset($data['mail_encryption'])) {
            config(['mail.mailers.smtp.encryption' => $data['mail_encryption']]);
        }
        
        if (isset($data['mail_from_address'])) {
            config(['mail.from.address' => $data['mail_from_address']]);
        }
        
        if (isset($data['mail_from_name'])) {
            config(['mail.from.name' => $data['mail_from_name']]);
        }
    }
    
    // Scheduled Tasks Methods
    public function runScheduler(): void
    {
        try {
            Artisan::call('schedule:run');
            $output = Artisan::output();
            
            Notification::make()
                ->title('Scheduler Executed')
                ->body('Scheduled tasks have been executed. Check logs for details.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Scheduler Error')
                ->body('Failed to run scheduler: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function listScheduledTasks(): void
    {
        try {
            Artisan::call('schedule:list');
            $output = Artisan::output();
            
            Notification::make()
                ->title('Scheduled Tasks')
                ->body('Check the console output for the list of scheduled tasks.')
                ->info()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to list scheduled tasks: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function runCommand(string $command): void
    {
        try {
            Artisan::call($command);
            $output = Artisan::output();
            
            Notification::make()
                ->title('Command Executed')
                ->body('Command "' . $command . '" has been executed successfully.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Command Error')
                ->body('Failed to execute command "' . $command . '": ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}