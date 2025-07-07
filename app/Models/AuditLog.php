<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user_id',
        'target_user_id',
        'action',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the admin user who performed the action.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the target user who was affected.
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Create an audit log entry.
     */
    public static function log(
        int $adminUserId,
        string $action,
        ?int $targetUserId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null
    ): self {
        return static::create([
            'admin_user_id' => $adminUserId,
            'target_user_id' => $targetUserId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope by admin user.
     */
    public function scopeByAdmin($query, int $adminUserId)
    {
        return $query->where('admin_user_id', $adminUserId);
    }

    /**
     * Scope by target user.
     */
    public function scopeByTarget($query, int $targetUserId)
    {
        return $query->where('target_user_id', $targetUserId);
    }
}