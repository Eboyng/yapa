<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Widgets;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\WalletService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';
    
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
    // Navigation Badge
    // =============================
    
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 999 ? '999+' : (string) $count;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'primary';
    }
    
    // =============================
    // Global Search
    // =============================
    
    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->name;
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'whatsapp_number', 'location'];
    }
    
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Email' => $record->email,
            'Location' => $record->location ?? 'Not set',
            'Registered' => $record->created_at->diffForHumans(),
        ];
    }
    
    // =============================
    // Form Configuration
    // =============================
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        // User Information Card
                        Forms\Components\Section::make('User Profile')
                            ->description('Basic user information and contact details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('John Doe')
                                            ->autocomplete('name'),
                                            
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('john@example.com')
                                            ->autocomplete('email'),
                                            
                                        Forms\Components\TextInput::make('whatsapp_number')
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('+234 xxx xxx xxxx')
                                            ->helperText('Include country code'),
                                            
                                        Forms\Components\TextInput::make('location')
                                            ->maxLength(255)
                                            ->placeholder('Lagos, Nigeria')
                                            ->suffixIcon('heroicon-m-map-pin'),
                                    ])
                                    ->columns(2),
                                
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('bvn')
                                            ->label('BVN')
                                            ->maxLength(11)
                                            ->password()
                                            ->revealable()
                                            ->helperText('Bank Verification Number (encrypted)'),
                                            
                                        Forms\Components\FileUpload::make('avatar')
                                            ->label('Profile Picture')
                                            ->image()
                                            ->imageEditor()
                                            ->circleCropper()
                                            ->directory('avatars')
                                            ->visibility('public')
                                            ->maxSize(1024)
                                            ->helperText('Max 1MB, will be cropped to circle'),
                                    ])
                                    ->columns(2),
                            ])
                            ->collapsible()
                            ->persistCollapsed()
                            ->columnSpan(['lg' => 2]),
                        
                        // Quick Stats Card
                        Forms\Components\Section::make('Account Statistics')
                            ->description('User activity and engagement metrics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Placeholder::make('account_age')
                                    ->label('Account Age')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record 
                                            ? '<span class="text-lg font-semibold">' . $record->created_at->diffForHumans() . '</span>'
                                            : '-'
                                    )),
                                    
                                Forms\Components\Placeholder::make('last_active')
                                    ->label('Last Active')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record && $record->last_active_at
                                            ? '<span class="text-lg font-semibold">' . $record->last_active_at->diffForHumans() . '</span>'
                                            : '<span class="text-gray-500">Never</span>'
                                    )),
                                    
                                Forms\Components\Placeholder::make('total_batches')
                                    ->label('Batches Joined')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record 
                                            ? '<span class="text-lg font-semibold">' . $record->batches()->count() . '</span>'
                                            : '0'
                                    )),
                                    
                                Forms\Components\Placeholder::make('total_ads')
                                    ->label('Ads Participated')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record 
                                            ? '<span class="text-lg font-semibold">' . $record->adParticipations()->count() . '</span>'
                                            : '0'
                                    )),
                            ])
                            ->columns(2)
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(['lg' => 3]),
                
                // Wallet Management Section
                Forms\Components\Section::make('Wallet & Financial')
                    ->description('Manage user wallets and balances')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('credits_wallet')
                                            ->label('Credits Balance')
                                            ->content(fn (?User $record): HtmlString => new HtmlString(
                                                $record 
                                                    ? '<div class="flex items-center space-x-2">
                                                        <span class="text-2xl font-bold text-blue-600">' . 
                                                        number_format($record->getCreditWallet()->balance) . 
                                                        '</span>
                                                        <span class="text-sm text-gray-500">credits</span>
                                                       </div>'
                                                    : '-'
                                            )),
                                    ])
                                    ->columnSpan(1),
                                    
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('naira_wallet')
                                            ->label('Naira Balance')
                                            ->content(fn (?User $record): HtmlString => new HtmlString(
                                                $record 
                                                    ? '<div class="flex items-center space-x-2">
                                                        <span class="text-2xl font-bold text-green-600">₦' . 
                                                        number_format($record->getNairaWallet()->balance, 2) . 
                                                        '</span>
                                                       </div>'
                                                    : '-'
                                            )),
                                    ])
                                    ->columnSpan(1),
                                    
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('earnings_wallet')
                                            ->label('Earnings Balance')
                                            ->content(fn (?User $record): HtmlString => new HtmlString(
                                                $record 
                                                    ? '<div class="flex items-center space-x-2">
                                                        <span class="text-2xl font-bold text-orange-600">₦' . 
                                                        number_format($record->getEarningsWallet()->balance, 2) . 
                                                        '</span>
                                                       </div>'
                                                    : '-'
                                            )),
                                    ])
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
                
                // Account Settings
                Forms\Components\Section::make('Account Settings & Permissions')
                    ->description('Configure user access and notification preferences')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Tabs::make('Settings')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Notifications')
                                    ->icon('heroicon-o-bell')
                                    ->schema([
                                        Forms\Components\Toggle::make('whatsapp_notifications_enabled')
                                            ->label('WhatsApp Notifications')
                                            ->helperText('Receive notifications via WhatsApp')
                                            ->default(true)
                                            ->inline(false),
                                            
                                        Forms\Components\Toggle::make('email_notifications_enabled')
                                            ->label('Email Notifications')
                                            ->helperText('Receive notifications via email')
                                            ->default(true)
                                            ->inline(false),
                                            
                                        Forms\Components\Toggle::make('email_verification_enabled')
                                            ->label('Email Verification Required')
                                            ->helperText('User must verify email to access features')
                                            ->default(true)
                                            ->inline(false),
                                    ])
                                    ->columns(3),
                                    
                                Forms\Components\Tabs\Tab::make('Restrictions')
                                    ->icon('heroicon-o-shield-exclamation')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_flagged_for_ads')
                                            ->label('Flagged for Ad Spam')
                                            ->helperText('Prevents user from participating in ads')
                                            ->live()
                                            ->afterStateUpdated(fn ($state) => $state 
                                                ? Notification::make()
                                                    ->warning()
                                                    ->title('User Flagged')
                                                    ->body('User will be restricted from ad participation')
                                                    ->send()
                                                : null
                                            ),
                                            
                                        Forms\Components\Toggle::make('is_banned_from_batches')
                                            ->label('Banned from Batches')
                                            ->helperText('Prevents user from joining new batches')
                                            ->live(),
                                            
                                        Forms\Components\Textarea::make('ban_reason')
                                            ->label('Ban Reason')
                                            ->maxLength(1000)
                                            ->rows(3)
                                            ->placeholder('Provide detailed reason for the ban...')
                                            ->visible(fn (Forms\Get $get) => $get('is_banned_from_batches'))
                                            ->required(fn (Forms\Get $get) => $get('is_banned_from_batches')),
                                            
                                        Forms\Components\DateTimePicker::make('ban_expires_at')
                                            ->label('Ban Expiry')
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->minDate(now())
                                            ->helperText('Leave empty for permanent ban')
                                            ->visible(fn (Forms\Get $get) => $get('is_banned_from_batches')),
                                    ])
                                    ->columns(1),
                                    
                                Forms\Components\Tabs\Tab::make('Roles & Permissions')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        Forms\Components\Select::make('roles')
                                            ->label('User Roles')
                                            ->multiple()
                                            ->relationship('roles', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->unique(),
                                                Forms\Components\TextInput::make('guard_name')
                                                    ->default('web'),
                                            ])
                                            ->helperText('Assign roles to control user permissions'),
                                    ]),
                                    
                                Forms\Components\Tabs\Tab::make('Appeals & Reports')
                                    ->icon('heroicon-o-exclamation-circle')
                                    ->schema([
                                        Forms\Components\Textarea::make('appeal_message')
                                            ->label('User Appeal Message')
                                            ->maxLength(1000)
                                            ->rows(4)
                                            ->disabled()
                                            ->helperText('Message from user appealing restrictions'),
                                            
                                        Forms\Components\TextInput::make('ad_rejection_count')
                                            ->label('Ad Rejections')
                                            ->numeric()
                                            ->disabled()
                                            ->suffixIcon('heroicon-m-x-circle')
                                            ->helperText('Number of ads rejected by user'),
                                            
                                        Forms\Components\TextInput::make('otp_attempts')
                                            ->label('Failed OTP Attempts')
                                            ->numeric()
                                            ->disabled()
                                            ->suffixIcon('heroicon-m-exclamation-triangle')
                                            ->helperText('Failed login attempts'),
                                    ])
                                    ->columns(1),
                            ])
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
                    
                // Verification Status
                Forms\Components\Section::make('Verification Status')
                    ->description('User verification timestamps')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Placeholder::make('email_verification')
                                    ->label('Email Verified')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record && $record->email_verified_at
                                            ? '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                ' . $record->email_verified_at->format('d M Y, H:i') . '
                                               </span>'
                                            : '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Not Verified
                                               </span>'
                                    )),
                                    
                                Forms\Components\Placeholder::make('whatsapp_verification')
                                    ->label('WhatsApp Verified')
                                    ->content(fn (?User $record): HtmlString => new HtmlString(
                                        $record && $record->whatsapp_verified_at
                                            ? '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                ' . $record->whatsapp_verified_at->format('d M Y, H:i') . '
                                               </span>'
                                            : '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Not Verified
                                               </span>'
                                    )),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
    
    // =============================
    // Table Configuration
    // =============================
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn (User $record): string => 
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . 
                        '&color=7F9CF5&background=EBF4FF'
                    ),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->email)
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('WhatsApp number copied')
                    ->icon('heroicon-m-phone')
                    ->iconColor('success')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-map-pin')
                    ->iconColor('gray')
                    ->default('—')
                    ->toggleable(),
                    
                // Wallet Columns
                Tables\Columns\TextColumn::make('credits_balance')
                    ->label('Credits')
                    ->getStateUsing(fn (User $record): float => $record->getCreditWallet()->balance)
                    ->numeric()
                    ->sortable()
                    ->color('primary')
                    ->badge()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('naira_balance')
                    ->label('Naira')
                    ->getStateUsing(fn (User $record): float => $record->getNairaWallet()->balance)
                    ->money('NGN')
                    ->sortable()
                    ->color('success')
                    ->badge()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('earnings_balance')
                    ->label('Earnings')
                    ->getStateUsing(fn (User $record): float => $record->getEarningsWallet()->balance)
                    ->money('NGN')
                    ->sortable()
                    ->color('warning')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                // Status Columns
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (User $record): string => 
                        $record->email_verified_at 
                            ? 'Verified: ' . $record->email_verified_at->format('d M Y')
                            : 'Not verified'
                    ),
                    
                Tables\Columns\IconColumn::make('whatsapp_verified_at')
                    ->label('WA')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (User $record): string => 
                        $record->whatsapp_verified_at 
                            ? 'Verified: ' . $record->whatsapp_verified_at->format('d M Y')
                            : 'Not verified'
                    ),
                    
                Tables\Columns\IconColumn::make('is_flagged_for_ads')
                    ->label('Flagged')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn (User $record): string => 
                        $record->is_flagged_for_ads 
                            ? 'Flagged for ad spam'
                            : 'Not flagged'
                    ),
                    
                Tables\Columns\IconColumn::make('is_banned_from_batches')
                    ->label('Banned')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn (User $record): string => 
                        $record->is_banned_from_batches 
                            ? 'Banned: ' . ($record->ban_reason ?? 'No reason provided')
                            : 'Active'
                    ),
                    
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->color(fn (string $state): string => match($state) {
                        'admin' => 'danger',
                        'moderator' => 'warning',
                        'user' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->description(fn (User $record): string => $record->created_at->diffForHumans())
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn (?User $record): string => 
                        $record->last_active_at 
                            ? $record->last_active_at->diffForHumans()
                            : 'Never'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Date Filters
                Tables\Filters\Filter::make('created_today')
                    ->label('Registered Today')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereDate('created_at', Carbon::today())
                    ),
                    
                Tables\Filters\Filter::make('created_this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereBetween('created_at', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ])
                    ),
                    
                Tables\Filters\Filter::make('created_this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                    ),
                    
                // Location Filter
                Tables\Filters\SelectFilter::make('location')
                    ->label('Location')
                    ->multiple()
                    ->options(fn (): array => 
                        User::whereNotNull('location')
                            ->distinct()
                            ->pluck('location', 'location')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),
                    
                // Status Filters
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label('Email Verified')
                    ->placeholder('All users')
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                Tables\Filters\TernaryFilter::make('whatsapp_verified')
                    ->label('WhatsApp Verified')
                    ->placeholder('All users')
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('whatsapp_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('whatsapp_verified_at'),
                    ),
                    
                Tables\Filters\TernaryFilter::make('is_flagged_for_ads')
                    ->label('Ad Flag Status'),
                    
                Tables\Filters\TernaryFilter::make('is_banned_from_batches')
                    ->label('Batch Ban Status'),
                    
                // Wallet Filters
                Tables\Filters\Filter::make('has_credits')
                    ->label('Has Credits')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('wallets', function ($q) {
                            $q->where('type', 'credits')->where('balance', '>', 0);
                        })
                    ),
                    
                Tables\Filters\Filter::make('has_earnings')
                    ->label('Has Earnings')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('wallets', function ($q) {
                            $q->where('type', 'earnings')->where('balance', '>', 0);
                        })
                    ),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-m-eye'),
                        
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-m-pencil-square'),
                        
                    // User Management Actions
                    static::getFlagAction(),
                    static::getBanAction(),
                    static::getImpersonateAction(),
                    
                    // Wallet Actions
                    static::getFundWalletAction(),
                    static::getDeductWalletAction(),
                    
                    // Role Management
                    static::getAssignRolesAction(),
                    
                    // Danger Zone
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Delete User')
                        ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                        ->action(function (User $record) {
                            static::logUserAction('user_deleted', $record);
                            $record->delete();
                            
                            Notification::make()
                                ->title('User deleted')
                                ->success()
                                ->send();
                        }),
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Export WhatsApp Numbers as VCF
                    static::getExportVcfBulkAction(),
                    
                    // Bulk Flag/Unflag
                    BulkAction::make('bulk_flag')
                        ->label('Flag Selected')
                        ->icon('heroicon-o-flag')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_flagged_for_ads' => true]);
                            
                            Notification::make()
                                ->title('Users flagged')
                                ->body($records->count() . ' users have been flagged')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    BulkAction::make('bulk_unflag')
                        ->label('Unflag Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_flagged_for_ads' => false]);
                            
                            Notification::make()
                                ->title('Users unflagged')
                                ->body($records->count() . ' users have been unflagged')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    // Bulk Delete
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->extremePaginationLinks()
            ->selectCurrentPageOnly();
    }
    
    // =============================
    // Custom Actions
    // =============================
    
    protected static function getFlagAction(): Action
    {
        return Action::make('toggle_flag')
            ->label(fn (User $record): string => 
                $record->is_flagged_for_ads ? 'Unflag User' : 'Flag for Spam'
            )
            ->icon(fn (User $record): string => 
                $record->is_flagged_for_ads ? 'heroicon-o-check-circle' : 'heroicon-o-flag'
            )
            ->color(fn (User $record): string => 
                $record->is_flagged_for_ads ? 'success' : 'danger'
            )
            ->requiresConfirmation()
            ->modalHeading(fn (User $record): string => 
                $record->is_flagged_for_ads ? 'Unflag User' : 'Flag User for Spam'
            )
            ->modalDescription(fn (User $record): string => 
                $record->is_flagged_for_ads 
                    ? 'Remove the spam flag from this user?'
                    : 'Flag this user for spam? They will be restricted from ads.'
            )
            ->action(function (User $record) {
                $newStatus = !$record->is_flagged_for_ads;
                $record->update([
                    'is_flagged_for_ads' => $newStatus,
                    'flagged_at' => $newStatus ? now() : null,
                ]);
                
                static::logUserAction(
                    $newStatus ? 'user_flagged_for_spam' : 'user_unflagged',
                    $record
                );
                
                Notification::make()
                    ->title($newStatus ? 'User flagged' : 'User unflagged')
                    ->success()
                    ->send();
            });
    }
    
    protected static function getBanAction(): Action
    {
        return Action::make('toggle_ban')
            ->label(fn (User $record): string => 
                $record->is_banned_from_batches ? 'Unban from Batches' : 'Ban from Batches'
            )
            ->icon(fn (User $record): string => 
                $record->is_banned_from_batches ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol'
            )
            ->color(fn (User $record): string => 
                $record->is_banned_from_batches ? 'success' : 'danger'
            )
            ->form(fn (User $record): array => 
                $record->is_banned_from_batches ? [] : [
                    Forms\Components\Textarea::make('ban_reason')
                        ->label('Ban Reason')
                        ->required()
                        ->maxLength(1000)
                        ->rows(3)
                        ->placeholder('Provide a clear reason for the ban...'),
                        
                    Forms\Components\DateTimePicker::make('ban_expires_at')
                        ->label('Ban Expiry (Optional)')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->minDate(now()->addHour())
                        ->helperText('Leave empty for permanent ban'),
                ]
            )
            ->requiresConfirmation()
            ->action(function (User $record, array $data) {
                if ($record->is_banned_from_batches) {
                    $record->unbanFromBatches();
                    $action = 'user_unbanned_from_batches';
                    $message = 'User unbanned from batches';
                } else {
                    $record->banFromBatches($data['ban_reason'], $data['ban_expires_at'] ?? null);
                    $action = 'user_banned_from_batches';
                    $message = 'User banned from batches';
                }
                
                static::logUserAction($action, $record, $data);
                
                Notification::make()
                    ->title($message)
                    ->success()
                    ->send();
            });
    }
    
    protected static function getImpersonateAction(): Action
    {
        return Action::make('impersonate')
            ->label('Impersonate')
            ->icon('heroicon-o-user-circle')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Impersonate User')
            ->modalDescription('You will be logged in as this user. Use the "Stop Impersonating" button to return.')
            ->action(function (User $record) {
                static::logUserAction('user_impersonated', $record);
                
                session(['impersonating_admin_id' => Auth::id()]);
                Auth::login($record);
                
                Notification::make()
                    ->title('Now impersonating')
                    ->body("You are now logged in as {$record->name}")
                    ->success()
                    ->send();
                
                redirect()->to('/dashboard');
            });
    }
    
    protected static function getFundWalletAction(): Action
    {
        return Action::make('fund_wallet')
            ->label('Fund Wallet')
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
                    ->step(0.01)
                    ->prefix(fn (Forms\Get $get): string => 
                        $get('wallet_type') === 'credits' ? '' : '₦'
                    ),
                    
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->rows(2)
                    ->placeholder('Reason for funding...'),
            ])
            ->action(function (User $record, array $data) {
                $walletService = app(WalletService::class);
                
                try {
                    $walletService->fundWallet(
                        $record,
                        $data['wallet_type'],
                        $data['amount'],
                        $data['description'] ?? 'Admin funding'
                    );
                    
                    static::logUserAction('wallet_funded', $record, $data);
                    
                    Notification::make()
                        ->title('Wallet funded')
                        ->body("Added {$data['amount']} to {$data['wallet_type']} wallet")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
    
    protected static function getDeductWalletAction(): Action
    {
        return Action::make('deduct_wallet')
            ->label('Deduct from Wallet')
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
                    ->step(0.01)
                    ->prefix(fn (Forms\Get $get): string => 
                        $get('wallet_type') === 'credits' ? '' : '₦'
                    ),
                    
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->rows(2)
                    ->required()
                    ->placeholder('Reason for deduction...'),
            ])
            ->requiresConfirmation()
            ->action(function (User $record, array $data) {
                $walletService = app(WalletService::class);
                
                try {
                    $walletService->deductWallet(
                        $record,
                        $data['wallet_type'],
                        $data['amount'],
                        $data['description']
                    );
                    
                    static::logUserAction('wallet_deducted', $record, $data);
                    
                    Notification::make()
                        ->title('Amount deducted')
                        ->body("Deducted {$data['amount']} from {$data['wallet_type']} wallet")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
    
    protected static function getAssignRolesAction(): Action
    {
        return Action::make('assign_roles')
            ->label('Manage Roles')
            ->icon('heroicon-o-shield-check')
            ->color('info')
            ->form([
                Forms\Components\CheckboxList::make('roles')
                    ->label('User Roles')
                    ->options(Role::pluck('name', 'name'))
                    ->columns(2)
                    ->default(fn (User $record) => $record->roles->pluck('name')->toArray())
                    ->helperText('Select roles to assign to this user'),
            ])
            ->action(function (User $record, array $data) {
                $record->syncRoles($data['roles'] ?? []);
                
                static::logUserAction('roles_updated', $record, $data);
                
                Notification::make()
                    ->title('Roles updated')
                    ->success()
                    ->send();
            });
    }
    
    protected static function getExportVcfBulkAction(): BulkAction
    {
        return BulkAction::make('export_vcf')
            ->label('Export as VCF')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->action(function (Collection $records) {
                $vcfContent = "BEGIN:VCARD\nVERSION:3.0\n";
                
                foreach ($records as $user) {
                    if ($user->whatsapp_number) {
                        $vcfContent .= "FN:{$user->name}\n";
                        $vcfContent .= "TEL;TYPE=CELL:{$user->whatsapp_number}\n";
                        $vcfContent .= "EMAIL:{$user->email}\n";
                        if ($user->location) {
                            $vcfContent .= "ADR;TYPE=HOME:;;{$user->location};;;;\n";
                        }
                        $vcfContent .= "END:VCARD\n";
                        $vcfContent .= "BEGIN:VCARD\nVERSION:3.0\n";
                    }
                }
                
                $vcfContent = rtrim($vcfContent, "BEGIN:VCARD\nVERSION:3.0\n");
                
                $fileName = 'users_export_' . now()->format('Y-m-d_His') . '.vcf';
                
                return response()->streamDownload(
                    function () use ($vcfContent) {
                        echo $vcfContent;
                    },
                    $fileName,
                    [
                        'Content-Type' => 'text/vcard',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ]
                );
            })
            ->deselectRecordsAfterCompletion();
    }
    
    // =============================
    // Helper Methods
    // =============================
    
    protected static function logUserAction(string $action, User $user, array $metadata = []): void
    {
        AuditLog::create([
            'admin_user_id' => Auth::id(),
            'target_user_id' => $user->id,
            'action' => $action,
            'description' => str_replace('_', ' ', ucfirst($action)) . ' for ' . $user->name,
            'metadata' => $metadata,
        ]);
    }
    
    // =============================
    // Widgets
    // =============================
    
    public static function getWidgets(): array
    {
        return [
            Widgets\UserStatsOverview::class,
        ];
    }
    
    // =============================
    // Pages
    // =============================
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    // =============================
    // Relations
    // =============================
    
    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
        ];
    }
}