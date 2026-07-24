<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasSlug, SoftDeletes;

    public const DIFFICULTIES = ['easy', 'medium', 'expert'];

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'title', 'slug', 'description', 'banner_image', 'difficulty',
        'duration_minutes', 'available_from', 'available_until', 'status',
        'shuffle_questions', 'show_explanations', 'require_login', 'allow_retake',
        'max_attempts', 'pass_percentage', 'points_complete', 'points_perfect_bonus',
        'points_high_score_bonus', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'shuffle_questions' => 'boolean',
            'show_explanations' => 'boolean',
            'require_login' => 'boolean',
            'allow_retake' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('display_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeAvailableNow(Builder $query): Builder
    {
        $now = now();

        return $query->published()
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('available_from')->orWhere('available_from', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('available_until')->orWhere('available_until', '>=', $now);
            });
    }

    public function isAvailableNow(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }
        if ($this->available_from && $this->available_from->isFuture()) {
            return false;
        }
        if ($this->available_until && $this->available_until->isPast()) {
            return false;
        }

        return true;
    }

    public function userAttemptCount(?int $userId): int
    {
        if (! $userId) {
            return 0;
        }

        return $this->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'timed_out'])
            ->count();
    }

    public function canUserAttempt(?User $user): bool
    {
        if (! $this->isAvailableNow()) {
            return false;
        }
        if ($this->require_login && ! $user) {
            return false;
        }
        if ($user && $this->max_attempts !== null) {
            return $this->userAttemptCount($user->id) < $this->max_attempts;
        }
        if ($user && ! $this->allow_retake && $this->userAttemptCount($user->id) > 0) {
            return false;
        }

        return true;
    }

    public static function questionTypes(): array
    {
        return QuizQuestion::TYPES;
    }
}
