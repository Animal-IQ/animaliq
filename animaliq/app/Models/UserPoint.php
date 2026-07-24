<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = ['user_id', 'action', 'points', 'source_type', 'source_id', 'occurred_at'];

    protected $casts = ['occurred_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Point values for every action
    public static function pointsFor(string $action): int
    {
        return match($action) {
            'account_created'        => 5,
            // Blog — actions you take
            'blog_view'              => 1,
            'blog_like'              => 2,
            'blog_bookmark'          => 3,
            'blog_comment'           => 5,
            'blog_comment_like'      => 1,
            // Blog — your content receiving engagement
            'post_published'         => 20,
            'post_received_like'     => 2,
            'post_received_bookmark' => 2,
            'post_received_comment'  => 3,
            'post_received_view'     => 1,
            // Forum — actions you take
            'forum_post'             => 10,
            'forum_like'             => 2,
            'forum_bookmark'         => 3,
            'forum_comment'          => 5,
            'forum_comment_like'     => 1,
            // Forum — your content receiving engagement
            'forum_received_like'     => 2,
            'forum_received_bookmark' => 2,
            'forum_received_comment'  => 3,
            // Research
            'research_published'     => 25,
            // Events & donations
            'event_register'         => 8,
            'donation'               => 15,
            // Sharing
            'share'                  => 3,
            // Quizzes
            'quiz_complete'          => 8,
            'quiz_score'             => 0, // dynamic override
            'quiz_high_score'        => 10,
            'quiz_perfect'           => 15,
            default                  => 0,
        };
    }

    /**
     * Record a point event, ignoring duplicates silently.
     * Pass $pointsOverride to store a custom amount (e.g. quiz score scaling).
     */
    public static function record(
        int $userId,
        string $action,
        string $sourceType = null,
        int $sourceId = null,
        \Carbon\Carbon $occurredAt = null,
        ?int $pointsOverride = null
    ): void {
        $points = $pointsOverride ?? self::pointsFor($action);
        if ($points === 0) {
            return;
        }

        try {
            self::firstOrCreate(
                [
                    'user_id'     => $userId,
                    'action'      => $action,
                    'source_type' => $sourceType,
                    'source_id'   => $sourceId,
                ],
                [
                    'points'      => $points,
                    'occurred_at' => $occurredAt ?? now(),
                ]
            );
        } catch (\Illuminate\Database\QueryException) {
            // Duplicate — silently ignore
        }
    }
}
