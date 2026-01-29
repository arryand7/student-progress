<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'evaluator_id',
        'week_number',
        'year',
        'evaluation_date',
        'notes',
        'total_score',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected function casts(): array
    {
        return [
            'evaluation_date' => 'date',
            'total_score' => 'decimal:2',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment this evaluation belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the evaluator (pembina) who created this evaluation.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Get the user who locked this evaluation.
     */
    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Get the evaluation details (component scores).
     */
    public function details(): HasMany
    {
        return $this->hasMany(EvaluationDetail::class);
    }

    /**
     * Scope a query to only include locked evaluations.
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope a query to only include unlocked evaluations.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope a query to filter by week and year.
     */
    public function scopeForWeek($query, int $week, int $year)
    {
        return $query->where('week_number', $week)->where('year', $year);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Lock the evaluation.
     */
    public function lock(User $user): void
    {
        $this->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);
    }

    /**
     * Unlock the evaluation (superadmin only).
     */
    public function unlock(): void
    {
        $this->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
        ]);
    }

    /**
     * Check if evaluation can be edited (same day and not locked).
     */
    public function canBeEdited(): bool
    {
        if ($this->is_locked) {
            return false;
        }

        return $this->evaluation_date
            ? $this->evaluation_date->isSameDay(now())
            : $this->created_at?->isSameDay(now());
    }

    /**
     * Check if evaluation can be edited by a specific user.
     */
    public function canBeEditedBy(User $user): bool
    {
        if ($this->is_locked) {
            return false;
        }

        if ($user->isSuperadmin()) {
            return true;
        }

        return $this->canBeEdited();
    }

    /**
     * Calculate and update the total weighted score.
     */
    public function calculateTotalScore(): void
    {
        $subject = $this->enrollment->subject;
        $components = $subject->activeComponents()->get();

        $weightedSum = 0;
        $scoredWeight = 0;

        foreach ($components as $component) {
            $detail = $this->details()->where('component_id', $component->id)->first();
            if ($detail && $detail->score !== null) {
                $weightedSum += ($detail->score * $component->weight);
                $scoredWeight += $component->weight;
            }
        }

        if ($scoredWeight == 0) {
            $this->update(['total_score' => null]);
            return;
        }

        $totalScore = $weightedSum / $scoredWeight;
        $this->update(['total_score' => round($totalScore, 2)]);
    }

    /**
     * Get student through enrollment.
     */
    public function getStudentAttribute()
    {
        return $this->enrollment->user;
    }

    /**
     * Get subject through enrollment.
     */
    public function getSubjectAttribute()
    {
        return $this->enrollment->subject;
    }

    /**
     * Get week label (e.g., "Week 4, 2024").
     */
    public function getWeekLabelAttribute(): string
    {
        return "Week {$this->week_number}, {$this->year}";
    }
}
