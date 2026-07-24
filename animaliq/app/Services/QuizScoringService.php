<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizQuestion;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;

class QuizScoringService
{
    public function startAttempt(Quiz $quiz, ?int $userId, ?string $guestToken = null): QuizAttempt
    {
        $questions = $quiz->questions()->get();
        $order = $questions->pluck('id')->all();
        if ($quiz->shuffle_questions) {
            shuffle($order);
        }

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'status' => 'in_progress',
            'started_at' => now(),
            'max_score' => $questions->sum('points'),
            'question_order' => $order,
        ]);
    }

    public function submitAnswer(QuizAttempt $attempt, QuizQuestion $question, mixed $answerPayload): QuizAttemptAnswer
    {
        if ($attempt->status !== 'in_progress') {
            abort(422, 'This attempt is already closed.');
        }
        if ($attempt->isTimedOut()) {
            $this->finalize($attempt, 'timed_out');
            abort(422, 'Time is up for this quiz.');
        }
        if ($question->quiz_id !== $attempt->quiz_id) {
            abort(404);
        }

        $result = $question->grade($answerPayload);

        return QuizAttemptAnswer::updateOrCreate(
            [
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $question->id,
            ],
            [
                'answer' => is_array($answerPayload) ? $answerPayload : ['value' => $answerPayload],
                'is_correct' => $result['is_correct'],
                'points_earned' => $result['points_earned'],
            ]
        );
    }

    public function finalize(QuizAttempt $attempt, string $status = 'completed'): QuizAttempt
    {
        return DB::transaction(function () use ($attempt, $status) {
            $attempt->refresh();
            if (in_array($attempt->status, ['completed', 'timed_out', 'abandoned'], true)) {
                return $attempt;
            }

            $answers = $attempt->answers()->get();
            $score = (int) $answers->sum('points_earned');
            $max = (int) ($attempt->max_score ?: $attempt->quiz->questions()->sum('points'));
            $percentage = $max > 0 ? round(($score / $max) * 100, 2) : 0;
            $spent = max(0, (int) $attempt->started_at->diffInSeconds(now()));

            $attempt->update([
                'status' => $status,
                'completed_at' => now(),
                'score' => $score,
                'max_score' => $max,
                'percentage' => $percentage,
                'time_spent_seconds' => $spent,
            ]);

            if (in_array($status, ['completed', 'timed_out'], true) && $attempt->user_id) {
                $this->awardPoints($attempt->fresh('quiz'));
            }

            return $attempt->fresh();
        });
    }

    public function awardPoints(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        $userId = $attempt->user_id;
        if (! $userId || ! $quiz) {
            return;
        }

        // Base completion points (once per attempt)
        UserPoint::record(
            $userId,
            'quiz_complete',
            'quiz_attempt',
            $attempt->id,
            $attempt->completed_at,
            (int) $quiz->points_complete
        );

        // Scaled score points: 0–20 based on percentage
        $scorePts = (int) round(((float) $attempt->percentage / 100) * 20);
        if ($scorePts > 0) {
            UserPoint::record(
                $userId,
                'quiz_score',
                'quiz_attempt',
                $attempt->id,
                $attempt->completed_at,
                $scorePts
            );
        }

        if ((float) $attempt->percentage >= (float) $quiz->pass_percentage) {
            UserPoint::record(
                $userId,
                'quiz_high_score',
                'quiz_attempt',
                $attempt->id,
                $attempt->completed_at,
                (int) $quiz->points_high_score_bonus
            );
        }

        if ((float) $attempt->percentage >= 100) {
            UserPoint::record(
                $userId,
                'quiz_perfect',
                'quiz_attempt',
                $attempt->id,
                $attempt->completed_at,
                (int) $quiz->points_perfect_bonus
            );
        }
    }
}
