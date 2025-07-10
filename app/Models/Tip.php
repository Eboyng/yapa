<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Tip extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'image',
        'content',
        'author_id',
        'status',
        'published_at',
        'claps',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'published_at' => 'datetime',
        'claps' => 'integer',
    ];

    /**
     * Tip statuses.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-generate slug if not provided
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = Str::slug($model->title);
                
                // Ensure slug is unique
                $originalSlug = $model->slug;
                $counter = 1;
                while (static::where('slug', $model->slug)->exists()) {
                    $model->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($model) {
            // Update slug if title changed
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
                $newSlug = Str::slug($model->title);
                
                // Ensure slug is unique (excluding current record)
                $originalSlug = $newSlug;
                $counter = 1;
                while (static::where('slug', $newSlug)->where('id', '!=', $model->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $model->slug = $newSlug;
            }
        });
    }

    /**
     * Get the author that owns the tip.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope to get only published tips.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                    ->where(function ($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    /**
     * Check if the tip is published.
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && 
               ($this->published_at === null || $this->published_at <= now());
    }

    /**
     * Check if the tip is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the tip is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Mark the tip as published.
     */
    public function markAsPublished(): bool
    {
        return $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => $this->published_at ?? now(),
        ]);
    }

    /**
     * Mark the tip as archived.
     */
    public function markAsArchived(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    /**
     * Increment claps count.
     */
    public function incrementClaps(): bool
    {
        return $this->increment('claps');
    }

    /**
     * Get formatted claps count.
     */
    public function getFormattedClapsAttribute(): string
    {
        if ($this->claps >= 1000000) {
            return number_format($this->claps / 1000000, 1) . 'M';
        } elseif ($this->claps >= 1000) {
            return number_format($this->claps / 1000, 1) . 'K';
        }
        return number_format($this->claps);
    }

    /**
     * Get content preview (first 300 words).
     */
    public function getContentPreviewAttribute(): string
    {
        $stripped = strip_tags($this->content);
        $words = explode(' ', $stripped);
        
        if (count($words) <= 300) {
            return $stripped;
        }
        
        return implode(' ', array_slice($words, 0, 300)) . '...';
    }

    /**
     * Get SEO description (first 150 characters).
     */
    public function getSeoDescriptionAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'green',
            self::STATUS_DRAFT => 'yellow',
            self::STATUS_ARCHIVED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}