<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the program this subject belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get pembina assigned to this subject.
     */
    public function pembinas(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pembina_subject')->withTimestamps();
    }

    /**
     * Get the components for this subject.
     */
    public function components(): HasMany
    {
        return $this->hasMany(Component::class)->orderBy('sort_order');
    }

    /**
     * Get active components for this subject.
     */
    public function activeComponents(): HasMany
    {
        return $this->hasMany(Component::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get the enrollments for this subject.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total weight of all active components.
     */
    public function getTotalWeightAttribute(): float
    {
        return $this->activeComponents()->sum('weight');
    }

    /**
     * Check if subject has any historical data (evaluations).
     */
    public function hasHistoricalData(): bool
    {
        return $this->enrollments()
            ->whereHas('evaluations')
            ->exists();
    }
}
