<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Interest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($interest) {
            if (empty($interest->slug)) {
                $interest->slug = static::generateUniqueSlug($interest->name);
            }
            if (is_null($interest->is_active)) {
                $interest->is_active = true;
            }
            if (is_null($interest->sort_order)) {
                $interest->sort_order = static::max('sort_order') + 1;
            }
        });

        static::updating(function ($interest) {
            if ($interest->isDirty('name') && !$interest->isDirty('slug')) {
                $interest->slug = static::generateUniqueSlug($interest->name);
            }
        });
    }

    /**
     * Get the users that have this interest.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_interests')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active interests.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to search interests by name or description.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the interest's display name with icon.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->icon ? $this->icon . ' ' . $this->name : $this->name;
    }

    /**
     * Get the interest's color or default.
     */
    public function getColorAttribute($value): string
    {
        return $value ?: '#3B82F6'; // Default blue color
    }

    /**
     * Get the interest's icon or default.
     */
    public function getIconAttribute($value): string
    {
        return $value ?: '🏷️'; // Default tag emoji
    }

    /**
     * Generate a unique slug for the interest.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Create or update interest with slug generation.
     */
    public static function createWithSlug(array $data): self
    {
        if (empty($data['slug'])) {
            $data['slug'] = static::generateUniqueSlug($data['name']);
        }

        return static::create($data);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}