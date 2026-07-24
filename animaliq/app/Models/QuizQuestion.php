<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    public const TYPES = [
        'who_am_i' => 'Who Am I?',
        'case_file' => 'Wildlife Case File',
        'story_adventure' => 'Story Adventure',
        'fact_fiction' => 'Fact or Fiction',
        'silhouette' => 'Animal Silhouette',
        'match_tracks' => 'Match the Tracks',
        'animal_vs_animal' => 'Animal vs Animal',
        'odd_one_out' => 'Odd One Out',
        'rank_them' => 'Rank Them',
        'multiple_choice' => 'Multiple Choice',
    ];

    protected $fillable = [
        'quiz_id', 'type', 'prompt', 'payload', 'explanation',
        'image_path', 'difficulty', 'points', 'display_order',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Grade a submitted answer. Returns [is_correct, points_earned, feedback].
     */
    public function grade(mixed $answer): array
    {
        $payload = $this->payload ?? [];
        $max = (int) $this->points;
        $isCorrect = false;
        $points = 0;
        $feedback = $this->explanation;
        $answer = is_array($answer) ? $answer : ['text' => $answer];

        switch ($this->type) {
            case 'who_am_i':
            case 'silhouette':
                $expected = strtolower(trim((string) ($payload['answer'] ?? '')));
                $given = strtolower(trim((string) ($answer['text'] ?? '')));
                $isCorrect = $expected !== '' && $given !== '' && ($given === $expected || $this->fuzzyMatch($given, $expected));
                // Optional multiple choice for silhouette / who_am_i
                if (array_key_exists('option_index', $answer) && isset($payload['options'])) {
                    $idx = (int) $answer['option_index'];
                    $opts = $payload['options'] ?? [];
                    $isCorrect = isset($opts[$idx]) && strtolower(trim((string) $opts[$idx])) === $expected;
                }
                break;

            case 'case_file':
            case 'multiple_choice':
            case 'odd_one_out':
                $correct = $payload['correct_index'] ?? null;
                $isCorrect = $correct !== null && (int) ($answer['option_index'] ?? -1) === (int) $correct;
                break;

            case 'fact_fiction':
                $correct = (bool) ($payload['correct'] ?? false);
                $isCorrect = array_key_exists('value', (array) $answer)
                    && (bool) $answer['value'] === $correct;
                break;

            case 'story_adventure':
                $choices = $payload['choices'] ?? [];
                $idx = (int) ($answer['choice_index'] ?? -1);
                $isCorrect = isset($choices[$idx]) && ! empty($choices[$idx]['is_best']);
                if (isset($choices[$idx]['outcome'])) {
                    $feedback = $choices[$idx]['outcome'] . ($this->explanation ? "\n\n" . $this->explanation : '');
                }
                break;

            case 'match_tracks':
                $pairs = $payload['pairs'] ?? [];
                $matches = $answer['matches'] ?? []; // [trackIndex => animalIndex] or animal labels
                $correctCount = 0;
                $total = count($pairs);
                foreach ($pairs as $i => $pair) {
                    $expected = strtolower(trim((string) ($pair['animal'] ?? '')));
                    $given = strtolower(trim((string) ($matches[$i] ?? '')));
                    if ($expected !== '' && $given === $expected) {
                        $correctCount++;
                    }
                }
                $isCorrect = $total > 0 && $correctCount === $total;
                $points = $total > 0 ? (int) round(($correctCount / $total) * $max) : 0;
                break;

            case 'animal_vs_animal':
                $categories = $payload['categories'] ?? [];
                $picks = $answer['picks'] ?? []; // [catIndex => 'a'|'b']
                $correctCount = 0;
                $total = count($categories);
                foreach ($categories as $i => $cat) {
                    $winner = strtolower((string) ($cat['winner'] ?? ''));
                    $pick = strtolower((string) ($picks[$i] ?? ''));
                    if ($winner !== '' && $pick === $winner) {
                        $correctCount++;
                    }
                }
                $isCorrect = $total > 0 && $correctCount === $total;
                $points = $total > 0 ? (int) round(($correctCount / $total) * $max) : 0;
                break;

            case 'rank_them':
                $correctOrder = array_map(
                    fn ($v) => strtolower(trim((string) $v)),
                    array_values($payload['correct_order'] ?? [])
                );
                $rawOrder = $answer['order'] ?? [];
                if (is_string($rawOrder)) {
                    $rawOrder = preg_split('/\r\n|\r|\n/', $rawOrder) ?: [];
                }
                $givenOrder = array_map(
                    fn ($v) => strtolower(trim((string) $v)),
                    array_values((array) $rawOrder)
                );
                $isCorrect = $correctOrder !== [] && $correctOrder === $givenOrder;
                // Partial credit by position
                if (! $isCorrect && $correctOrder !== [] && count($givenOrder) === count($correctOrder)) {
                    $hits = 0;
                    foreach ($correctOrder as $i => $item) {
                        if (($givenOrder[$i] ?? null) === $item) {
                            $hits++;
                        }
                    }
                    $points = (int) round(($hits / count($correctOrder)) * $max);
                }
                break;
        }

        if ($points === 0 && $isCorrect) {
            $points = $max;
        }
        if (! $isCorrect && ! in_array($this->type, ['match_tracks', 'animal_vs_animal', 'rank_them'], true)) {
            $points = 0;
        }

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $points,
            'feedback' => $feedback,
            'correct_reveal' => $this->revealPayload(),
        ];
    }

    protected function fuzzyMatch(string $given, string $expected): bool
    {
        similar_text($given, $expected, $percent);

        return $percent >= 85;
    }

    public function revealPayload(): array
    {
        $p = $this->payload ?? [];

        return match ($this->type) {
            'who_am_i', 'silhouette' => ['answer' => $p['answer'] ?? null],
            'case_file', 'multiple_choice', 'odd_one_out' => [
                'correct_index' => $p['correct_index'] ?? null,
                'options' => $p['options'] ?? $p['items'] ?? [],
            ],
            'fact_fiction' => ['correct' => $p['correct'] ?? null],
            'story_adventure' => ['choices' => $p['choices'] ?? []],
            'match_tracks' => ['pairs' => $p['pairs'] ?? []],
            'animal_vs_animal' => [
                'animal_a' => $p['animal_a'] ?? null,
                'animal_b' => $p['animal_b'] ?? null,
                'categories' => $p['categories'] ?? [],
            ],
            'rank_them' => ['correct_order' => $p['correct_order'] ?? [], 'criterion' => $p['criterion'] ?? null],
            default => $p,
        };
    }

    /**
     * Human-readable correct answer for result review.
     */
    public function formatCorrectAnswer(): string
    {
        $p = $this->payload ?? [];

        return match ($this->type) {
            'who_am_i', 'silhouette' => (string) ($p['answer'] ?? '—'),
            'case_file', 'multiple_choice' => $this->optionLabel($p['options'] ?? [], $p['correct_index'] ?? null),
            'odd_one_out' => $this->optionLabel($p['items'] ?? [], $p['correct_index'] ?? null),
            'fact_fiction' => ! empty($p['correct']) ? 'TRUE' : 'FALSE',
            'story_adventure' => (function () use ($p) {
                $best = collect($p['choices'] ?? [])->first(fn ($c) => ! empty($c['is_best']));

                return $best['text'] ?? '—';
            })(),
            'match_tracks' => collect($p['pairs'] ?? [])
                ->map(fn ($pair) => trim(($pair['track'] ?? '?') . ' → ' . ($pair['animal'] ?? '?')))
                ->filter()
                ->implode("\n") ?: '—',
            'animal_vs_animal' => collect($p['categories'] ?? [])
                ->map(function ($cat) use ($p) {
                    $winner = ($cat['winner'] ?? 'a') === 'b'
                        ? ($p['animal_b'] ?? 'B')
                        : ($p['animal_a'] ?? 'A');

                    return ($cat['name'] ?? 'Category') . ': ' . $winner;
                })
                ->implode("\n") ?: '—',
            'rank_them' => implode(' → ', $p['correct_order'] ?? $p['items'] ?? []) ?: '—',
            default => '—',
        };
    }

    /**
     * Human-readable user answer for result review.
     */
    public function formatUserAnswer(?array $answer): string
    {
        if ($answer === null || $answer === []) {
            return 'Not answered';
        }

        $p = $this->payload ?? [];

        return match ($this->type) {
            'who_am_i', 'silhouette' => isset($answer['option_index'])
                ? $this->optionLabel($p['options'] ?? [], $answer['option_index'])
                : (trim((string) ($answer['text'] ?? '')) !== '' ? (string) $answer['text'] : 'Not answered'),
            'case_file', 'multiple_choice' => $this->optionLabel($p['options'] ?? [], $answer['option_index'] ?? null),
            'odd_one_out' => $this->optionLabel($p['items'] ?? [], $answer['option_index'] ?? null),
            'fact_fiction' => array_key_exists('value', $answer)
                ? ((bool) $answer['value'] ? 'TRUE' : 'FALSE')
                : 'Not answered',
            'story_adventure' => (string) (($p['choices'][(int) ($answer['choice_index'] ?? -1)]['text'] ?? null) ?: 'Not answered'),
            'match_tracks' => collect($p['pairs'] ?? [])
                ->map(function ($pair, $i) use ($answer) {
                    $given = $answer['matches'][$i] ?? '—';

                    return trim(($pair['track'] ?? '?') . ' → ' . $given);
                })
                ->implode("\n") ?: 'Not answered',
            'animal_vs_animal' => collect($p['categories'] ?? [])
                ->map(function ($cat, $i) use ($p, $answer) {
                    $pick = strtolower((string) ($answer['picks'][$i] ?? ''));
                    $label = $pick === 'b'
                        ? ($p['animal_b'] ?? 'B')
                        : ($pick === 'a' ? ($p['animal_a'] ?? 'A') : '—');

                    return ($cat['name'] ?? 'Category') . ': ' . $label;
                })
                ->implode("\n") ?: 'Not answered',
            'rank_them' => (function () use ($answer) {
                $order = $answer['order'] ?? [];
                if (is_string($order)) {
                    $order = preg_split('/\r\n|\r|\n/', $order) ?: [];
                }
                $order = array_values(array_filter(array_map('trim', (array) $order)));

                return $order !== [] ? implode(' → ', $order) : 'Not answered';
            })(),
            default => '—',
        };
    }

    protected function optionLabel(array $options, mixed $index): string
    {
        if ($index === null || $index === '') {
            return 'Not answered';
        }
        $idx = (int) $index;

        return isset($options[$idx]) ? (string) $options[$idx] : 'Not answered';
    }
}
