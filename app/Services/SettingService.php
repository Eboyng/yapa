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
}