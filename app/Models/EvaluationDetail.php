<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'component_id',
        'score',
        'time_spent_minutes',
        'total_questions',
        'attempted_questions',
        'correct_questions',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * Get the evaluation this detail belongs to.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Get the component for this detail.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get attempt rate as percentage.
     */
    public function getAttemptRateAttribute(): ?float
    {
        if (!$this->total_questions || $this->total_questions == 0) {
            return null;
        }

        return round(($this->attempted_questions / $this->total_questions) * 100, 2);
    }

    /**
     * Get accuracy rate as percentage.
     */
    public function getAccuracyRateAttribute(): ?float
    {
        if (!$this->attempted_questions || $this->attempted_questions == 0) {
            return null;
        }

        return round(($this->correct_questions / $this->attempted_questions) * 100, 2);
    }

    /**
     * Get overall efficiency (correct / total) as percentage.
     */
    public function getEfficiencyRateAttribute(): ?float
    {
        if (!$this->total_questions || $this->total_questions == 0) {
            return null;
        }

        return round(($this->correct_questions / $this->total_questions) * 100, 2);
    }

    /**
     * Get time per question in minutes.
     */
    public function getTimePerQuestionAttribute(): ?float
    {
        if (!$this->attempted_questions || $this->attempted_questions == 0 || !$this->time_spent_minutes) {
            return null;
        }

        return round($this->time_spent_minutes / $this->attempted_questions, 2);
    }
}
