<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model.
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Create an audit log entry.
     */
    public static function log(
        string $action,
        Model $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($auditable),
            'auditable_id' => $auditable->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by auditable type.
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('auditable_type', $modelClass);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get human-readable action description.
     */
    public function getActionDescriptionAttribute(): string
    {
        $modelName = class_basename($this->auditable_type);
        
        return match ($this->action) {
            'created' => "Created {$modelName} #{$this->auditable_id}",
            'updated' => "Updated {$modelName} #{$this->auditable_id}",
            'deleted' => "Deleted {$modelName} #{$this->auditable_id}",
            'locked' => "Locked {$modelName} #{$this->auditable_id}",
            'unlocked' => "Unlocked {$modelName} #{$this->auditable_id}",
            'permission_granted' => "Permission granted on {$modelName}",
            'permission_revoked' => "Permission revoked on {$modelName}",
            'impersonated' => "Impersonated user #{$this->auditable_id}",
            default => "{$this->action} on {$modelName} #{$this->auditable_id}",
        };
    }
}
