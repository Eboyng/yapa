<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingService
{
    const CACHE_PREFIX = 'setting_';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Default settings.
     */
    protected array $defaults = [
        'admin_contact_name' => 'Yapa Admin',
        'admin_contact_number' => '+2348000000000',
        'batch_auto_close_days' => 7,
        'location_weight' => 60,
        'interests_weight' => 40,
        'trial_batch_limit' => 30,
        'regular_batch_limit' => 100,
        'google_people_cache_hours' => 24,
        'ads_feature_enabled' => true,
        'ad_earnings_per_view' => 0.3,
        'ad_screenshot_wait_hours' => 24,
        'max_ad_rejection_count' => 3,
        'appeal_cooldown_days' => 7,
        'whatsapp_notifications_enabled' => false,
        'email_notifications_enabled' => true,
        'app_name' => 'Yapa',
        'app_version' => '1.0.0',
        'maintenance_mode' => false,
        'registration_enabled' => true,
        'max_file_upload_size' => 5, // MB
        'supported_image_formats' => 'jpg,jpeg,png',
        'vcf_export_enabled' => true,
        'google_oauth_enabled' => false,
        // Site Branding Settings
        'site_name' => 'Yapa',
        'site_logo' => '',
        'site_favicon' => '',
        'site_logo_name' => 'Yapa',
        // Maintenance Mode Settings
        'maintenance_message' => 'We are currently performing scheduled maintenance. We will be back shortly.',
        'maintenance_end_time' => null,
        'maintenance_allowed_ips' => '',
        // SEO & OpenGraph Settings
        'seo_title' => 'Yapa - Connect & Share Contacts',
        'seo_description' => 'Join Yapa to connect with like-minded people, share contacts, and build meaningful networks.',
        'seo_keywords' => 'contacts, networking, social, sharing, community',
        'og_title' => 'Yapa - Connect & Share Contacts',
        'og_description' => 'Join Yapa to connect with like-minded people, share contacts, and build meaningful networks.',
        'og_image' => '',
        'og_type' => 'website',
        'twitter_card' => 'summary_large_image',
        'twitter_site' => '@yapa',
        'twitter_creator' => '@yapa',
        // Kudisms API Settings
        'kudisms_api_key' => '',
        'kudisms_whatsapp_template_code' => '',
        'kudisms_sender_id' => 'Yapa',
        'kudisms_whatsapp_url' => 'https://my.kudisms.net/api/whatsapp',
        // OTP Delivery Settings
        'otp_delivery_method' => 'whatsapp',
        'otp_sms_fallback_enabled' => true,
        // Kudisms SMS API Settings
        'kudisms_sms_template_code' => '',
        'kudisms_app_name_code' => '',
        'kudisms_sms_url' => 'https://my.kudisms.net/api/otp',
        'kudisms_balance_url' => 'https://my.kudisms.net/api/balance',
        // Paystack Payment Settings
        'paystack_public_key' => '',
        'paystack_secret_key' => '',
        'paystack_webhook_secret' => '',
        'paystack_environment' => 'test',
        'paystack_enabled' => true,
        'credit_price_naira' => 3.00,
        'minimum_credits_purchase' => 100,
        'minimum_amount_naira' => 300,
        // Email Configuration Settings
        'mail_mailer' => 'log',
        'mail_host' => '',
        'mail_port' => 587,
        'mail_username' => '',
        'mail_password' => '',
        'mail_encryption' => 'tls',
        'mail_from_address' => 'noreply@yoursite.com',
        'mail_from_name' => 'Yapa',
        // Banner Settings
        'banner_enabled' => true,
        'banner_auto_slide' => true,
        'banner_slide_interval' => 5000,
        'banner_guest_title' => 'Connect & Share Contacts',
        'banner_guest_subtitle' => 'Join Yapa to connect with like-minded people and build meaningful networks',
        'banner_guest_description' => 'Discover new connections, share your contacts, and grow your network with people who share your interests and location.',
        'banner_guest_primary_button_text' => 'Get Started',
        'banner_guest_primary_button_url' => '/register',
        'banner_guest_secondary_button_text' => 'Login',
        'banner_guest_secondary_button_url' => '/login',
        'banner_guest_background_image' => '',
        'banner_guest_background_type' => 'gradient',
        'banner_auth_title' => 'Join Our WhatsApp Community',
        'banner_auth_subtitle' => 'Stay connected with the latest updates and connect with other members',
        'banner_auth_description' => 'Get instant notifications about new batches, updates, and connect with fellow Yapa members.',
        'banner_auth_button_text' => 'Join WhatsApp Group',
        'banner_auth_button_url' => 'https://chat.whatsapp.com/your-group-link',
        'banner_auth_background_image' => '',
        'banner_auth_background_type' => 'gradient',
    ];

    /**
     * Get a setting value.
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $setting = DB::table('settings')->where('key', $key)->first();
            
            if ($setting) {
                return $this->castValue($setting->value, $setting->type ?? 'string');
            }
            
            return $default ?? ($this->defaults[$key] ?? null);
        });
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, $value, string $type = 'string'): bool
    {
        try {
            $serializedValue = $this->serializeValue($value, $type);
            
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $serializedValue,
                    'type' => $type,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            
            // Clear cache
            Cache::forget(self::CACHE_PREFIX . $key);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to set setting', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get multiple settings.
     */
    public function getMultiple(array $keys): array
    {
        $settings = [];
        
        foreach ($keys as $key) {
            $settings[$key] = $this->get($key);
        }
        
        return $settings;
    }

    /**
     * Set multiple settings.
     */
    public function setMultiple(array $settings): bool
    {
        try {
            DB::beginTransaction();
            
            foreach ($settings as $key => $data) {
                $value = $data['value'] ?? $data;
                $type = $data['type'] ?? 'string';
                
                $this->set($key, $value, $type);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Failed to set multiple settings', [
                'settings' => $settings,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Delete a setting.
     */
    public function delete(string $key): bool
    {
        try {
            DB::table('settings')->where('key', $key)->delete();
            Cache::forget(self::CACHE_PREFIX . $key);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete setting', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get all settings.
     */
    public function all(): array
    {
        $settings = [];
        
        // Get from database
        $dbSettings = DB::table('settings')->get();
        
        foreach ($dbSettings as $setting) {
            $settings[$setting->key] = $this->castValue($setting->value, $setting->type ?? 'string');
        }
        
        // Merge with defaults for missing keys
        foreach ($this->defaults as $key => $defaultValue) {
            if (!isset($settings[$key])) {
                $settings[$key] = $defaultValue;
            }
        }
        
        return $settings;
    }

    /**
     * Clear all setting caches.
     */
    public function clearCache(): void
    {
        $keys = array_keys($this->defaults);
        $dbKeys = DB::table('settings')->pluck('key')->toArray();
        $allKeys = array_unique(array_merge($keys, $dbKeys));
        
        foreach ($allKeys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }

    /**
     * Reset settings to defaults.
     */
    public function resetToDefaults(): bool
    {
        try {
            DB::table('settings')->delete();
            $this->clearCache();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to reset settings to defaults', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Initialize default settings in database.
     */
    public function initializeDefaults(): bool
    {
        try {
            foreach ($this->defaults as $key => $value) {
                $exists = DB::table('settings')->where('key', $key)->exists();
                
                if (!$exists) {
                    $type = $this->getValueType($value);
                    $this->set($key, $value, $type);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to initialize default settings', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Cast value to appropriate type.
     */
    protected function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
                return json_decode($value, true) ?? [];
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Serialize value for storage.
     */
    protected function serializeValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Get value type.
     */
    protected function getValueType($value): string
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

    /**
     * Get batch-related settings.
     */
    public function getBatchSettings(): array
    {
        return $this->getMultiple([
            'batch_auto_close_days',
            'location_weight',
            'interests_weight',
            'trial_batch_limit',
            'regular_batch_limit',
            'google_people_cache_hours',
            'admin_contact_name',
            'admin_contact_number',
            'vcf_export_enabled',
            'google_oauth_enabled',
        ]);
    }

    /**
     * Get ad-related settings.
     */
    public function getAdSettings(): array
    {
        return $this->getMultiple([
            'ad_earnings_per_view',
            'ad_screenshot_wait_hours',
            'max_ad_rejection_count',
            'appeal_cooldown_days',
            'max_file_upload_size',
            'supported_image_formats',
        ]);
    }

    /**
     * Get notification settings.
     */
    public function getNotificationSettings(): array
    {
        return $this->getMultiple([
            'whatsapp_notifications_enabled',
            'email_notifications_enabled',
        ]);
    }

    /**
     * Get Kudisms API settings.
     */
    public function getKudismsSettings(): array
    {
        return $this->getMultiple([
            'kudisms_api_key',
            'kudisms_whatsapp_template_code',
            'kudisms_sender_id',
            'kudisms_whatsapp_url',
            'kudisms_sms_template_code',
            'kudisms_app_name_code',
            'kudisms_sms_url',
            'kudisms_balance_url',
        ]);
    }

    /**
     * Get OTP delivery settings.
     */
    public function getOtpSettings(): array
    {
        return $this->getMultiple([
            'otp_delivery_method',
            'otp_sms_fallback_enabled',
        ]);
    }

    /**
     * Get SMS API settings.
     */
    public function getSmsSettings(): array
    {
        return $this->getMultiple([
            'kudisms_api_key',
            'kudisms_sender_id',
            'kudisms_sms_template_code',
            'kudisms_app_name_code',
            'kudisms_sms_url',
            'kudisms_balance_url',
        ]);
    }

    /**
     * Get app settings.
     */
    public function getAppSettings(): array
    {
        return $this->getMultiple([
            'app_name',
            'app_version',
            'maintenance_mode',
            'registration_enabled',
        ]);
    }

    /**
     * Check if feature is enabled.
     */
    public function isFeatureEnabled(string $feature): bool
    {
        $featureMap = [
            'whatsapp_notifications' => 'whatsapp_notifications_enabled',
            'email_notifications' => 'email_notifications_enabled',
            'vcf_export' => 'vcf_export_enabled',
            'google_oauth' => 'google_oauth_enabled',
            'registration' => 'registration_enabled',
            'maintenance' => 'maintenance_mode',
        ];
        
        $settingKey = $featureMap[$feature] ?? $feature;
        
        return (bool) $this->get($settingKey, false);
    }

    /**
     * Get weight settings for batch matching.
     */
    public function getMatchingWeights(): array
    {
        $locationWeight = $this->get('location_weight', 60);
        $interestsWeight = $this->get('interests_weight', 40);
        
        // Ensure weights add up to 100
        $total = $locationWeight + $interestsWeight;
        if ($total !== 100) {
            $locationWeight = 60;
            $interestsWeight = 40;
        }
        
        return [
            'location' => $locationWeight / 100,
            'interests' => $interestsWeight / 100,
        ];
    }

    /**
     * Get file upload limits.
     */
    public function getUploadLimits(): array
    {
        return [
            'max_size_mb' => $this->get('max_file_upload_size', 5),
            'max_size_bytes' => $this->get('max_file_upload_size', 5) * 1024 * 1024,
            'supported_formats' => explode(',', $this->get('supported_image_formats', 'jpg,jpeg,png')),
        ];
    }

    /**
     * Get site branding settings.
     */
    public function getBrandingSettings(): array
    {
        return $this->getMultiple([
            'site_name',
            'site_logo',
            'site_favicon',
            'site_logo_name',
        ]);
    }

    /**
     * Get maintenance mode settings.
     */
    public function getMaintenanceSettings(): array
    {
        return $this->getMultiple([
            'maintenance_mode',
            'maintenance_message',
            'maintenance_end_time',
            'maintenance_allowed_ips',
        ]);
    }

    /**
     * Get SEO and OpenGraph settings.
     */
    public function getSeoSettings(): array
    {
        return $this->getMultiple([
            'seo_title',
            'seo_description',
            'seo_keywords',
            'og_title',
            'og_description',
            'og_image',
            'og_type',
            'twitter_card',
            'twitter_site',
            'twitter_creator',
        ]);
    }

    /**
     * Check if site is in maintenance mode.
     */
    public function isMaintenanceMode(): bool
    {
        return (bool) $this->get('maintenance_mode', false);
    }

    /**
     * Check if IP is allowed during maintenance.
     */
    public function isIpAllowedDuringMaintenance(string $ip): bool
    {
        if (!$this->isMaintenanceMode()) {
            return true;
        }

        $allowedIps = $this->get('maintenance_allowed_ips', '');
        if (empty($allowedIps)) {
            return false;
        }

        $allowedIpsArray = array_map('trim', explode(',', $allowedIps));
        return in_array($ip, $allowedIpsArray);
    }

    /**
     * Get maintenance end time as Carbon instance.
     */
    public function getMaintenanceEndTime(): ?\Carbon\Carbon
    {
        $endTime = $this->get('maintenance_end_time');
        return $endTime ? \Carbon\Carbon::parse($endTime) : null;
    }

    /**
     * Check if maintenance period has ended.
     */
    public function isMaintenancePeriodEnded(): bool
    {
        $endTime = $this->getMaintenanceEndTime();
        return $endTime && $endTime->isPast();
    }

    /**
     * Get Paystack payment settings.
     */
    public function getPaystackSettings(): array
    {
        return $this->getMultiple([
            'paystack_public_key',
            'paystack_secret_key',
            'paystack_webhook_secret',
            'paystack_environment',
            'paystack_enabled',
            'credit_price_naira',
            'minimum_credits_purchase',
            'minimum_amount_naira',
        ]);
    }

    /**
     * Check if Paystack payments are enabled.
     */
    public function isPaystackEnabled(): bool
    {
        return (bool) $this->get('paystack_enabled', true);
    }

    /**
     * Get banner settings.
     */
    public function getBannerSettings(): array
    {
        return $this->getMultiple([
            'banner_enabled',
            'banner_auto_slide',
            'banner_slide_interval',
            'banner_guest_title',
            'banner_guest_subtitle',
            'banner_guest_description',
            'banner_guest_primary_button_text',
            'banner_guest_primary_button_url',
            'banner_guest_secondary_button_text',
            'banner_guest_secondary_button_url',
            'banner_guest_background_image',
            'banner_guest_background_type',
            'banner_auth_title',
            'banner_auth_subtitle',
            'banner_auth_description',
            'banner_auth_button_text',
            'banner_auth_button_url',
            'banner_auth_background_image',
            'banner_auth_background_type',
        ]);
    }

    /**
     * Check if banner is enabled.
     */
    public function isBannerEnabled(): bool
    {
        return (bool) $this->get('banner_enabled', true);
    }
}