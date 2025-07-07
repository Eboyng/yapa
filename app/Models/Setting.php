<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'float' => (float) $setting->value,
                'array' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        $formattedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'array' => json_encode($value),
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $formattedValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function all(): array
    {
        return Cache::remember('settings.all', 3600, function () {
            $settings = static::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = match ($setting->type) {
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $setting->value,
                    'float' => (float) $setting->value,
                    'array' => json_decode($setting->value, true),
                    default => $setting->value,
                };
            }
            
            return $result;
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::flush();
        });

        static::deleted(function () {
            Cache::flush();
        });
    }
}