<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id', 'user_id', 'guest_token', 'status', 'started_at', 'completed_at',
        'score', 'max_score', 'percentage', 'time_spent_seconds', 'question_order',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'percentage' => 'decimal:2',
            'question_order' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function isTimedOut(): bool
    {
        $quiz = $this->quiz;
        if (! $quiz || ! $quiz->duration_minutes || $this->status !== 'in_progress') {
            return false;
        }

        return $this->started_at->copy()->addMinutes($quiz->duration_minutes)->isPast();
    }

    public function secondsRemaining(): ?int
    {
        $quiz = $this->quiz;
        if (! $quiz?->duration_minutes) {
            return null;
        }
        $ends = $this->started_at->copy()->addMinutes($quiz->duration_minutes);

        return max(0, now()->diffInSeconds($ends, false));
    }
}
