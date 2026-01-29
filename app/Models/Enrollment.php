<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'subject_id',
        'status',
        'enrolled_at',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'date',
            'deactivated_at' => 'date',
        ];
    }

    /**
     * Get the user (student) for this enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user() - get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the program for this enrollment.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the subject for this enrollment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the evaluations for this enrollment.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class)->orderByDesc('year')->orderByDesc('week_number');
    }

    /**
     * Scope a query to only include active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive enrollments.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Deactivate the enrollment.
     */
    public function deactivate(): void
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Activate the enrollment.
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'deactivated_at' => null,
        ]);
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the latest evaluation for this enrollment.
     */
    public function latestEvaluation()
    {
        return $this->evaluations()->latest()->first();
    }

    /**
     * Get pending weeks without evaluation.
     */
    public function getPendingWeeks(int $year = null): array
    {
        $year = $year ?? now()->year;
        $currentWeek = now()->weekOfYear;

        $existingWeeks = $this->evaluations()
            ->where('year', $year)
            ->pluck('week_number')
            ->toArray();

        $pendingWeeks = [];
        for ($week = 1; $week <= $currentWeek; $week++) {
            if (!in_array($week, $existingWeeks)) {
                $pendingWeeks[] = $week;
            }
        }

        return $pendingWeeks;
    }
}
