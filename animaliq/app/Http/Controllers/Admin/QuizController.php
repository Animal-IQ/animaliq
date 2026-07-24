<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(15);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create', [
            'types' => QuizQuestion::TYPES,
            'difficulties' => Quiz::DIFFICULTIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('quizzes', 'public');
        }
        $data['created_by'] = $request->user()->id;
        $data['slug'] = $data['slug'] ?? null;
        $quiz = Quiz::create($data);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Quiz created. Add questions below.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions' => fn ($q) => $q->orderBy('display_order')]);

        return view('admin.quizzes.edit', [
            'quiz' => $quiz,
            'types' => QuizQuestion::TYPES,
            'difficulties' => Quiz::DIFFICULTIES,
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $this->validated($request, $quiz);
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('quizzes', 'public');
        }
        if ($request->boolean('remove_banner')) {
            $data['banner_image'] = null;
        }
        $quiz->update($data);

        return back()->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $data = $this->validatedQuestion($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('quizzes/questions', 'public');
        }
        $data['quiz_id'] = $quiz->id;
        $data['display_order'] = $quiz->questions()->max('display_order') + 1;
        $data['payload'] = $this->buildPayload($request, $data['type']);
        QuizQuestion::create($data);

        return back()->with('success', 'Question added.');
    }

    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        abort_unless($question->quiz_id === $quiz->id, 404);
        $data = $this->validatedQuestion($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('quizzes/questions', 'public');
        }
        $data['payload'] = $this->buildPayload($request, $data['type']);
        $question->update($data);

        return back()->with('success', 'Question updated.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
    {
        abort_unless($question->quiz_id === $quiz->id, 404);
        $question->delete();

        return back()->with('success', 'Question removed.');
    }

    public function reorderQuestions(Request $request, Quiz $quiz)
    {
        $order = $request->validate(['order' => 'required|array', 'order.*' => 'integer'])['order'];
        foreach ($order as $i => $id) {
            QuizQuestion::where('quiz_id', $quiz->id)->where('id', $id)->update(['display_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    protected function validated(Request $request, ?Quiz $quiz = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:quizzes,slug,' . ($quiz?->id ?? 'NULL'),
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|max:4096',
            'difficulty' => 'required|in:easy,medium,expert',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'status' => 'required|in:draft,published,archived',
            'shuffle_questions' => 'boolean',
            'show_explanations' => 'boolean',
            'require_login' => 'boolean',
            'allow_retake' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'pass_percentage' => 'nullable|integer|min:0|max:100',
            'points_complete' => 'nullable|integer|min:0|max:500',
            'points_perfect_bonus' => 'nullable|integer|min:0|max:500',
            'points_high_score_bonus' => 'nullable|integer|min:0|max:500',
        ]);

        foreach (['shuffle_questions', 'show_explanations', 'require_login', 'allow_retake'] as $bool) {
            $data[$bool] = $request->boolean($bool);
        }
        $data['pass_percentage'] = $data['pass_percentage'] ?? 50;
        $data['points_complete'] = $data['points_complete'] ?? 8;
        $data['points_perfect_bonus'] = $data['points_perfect_bonus'] ?? 15;
        $data['points_high_score_bonus'] = $data['points_high_score_bonus'] ?? 10;

        return $data;
    }

    protected function validatedQuestion(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(QuizQuestion::TYPES)),
            'prompt' => 'nullable|string|max:500',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'difficulty' => 'nullable|in:easy,medium,expert',
            'points' => 'nullable|integer|min:1|max:100',
        ]) + ['points' => $request->input('points', 10)];
    }

    protected function buildPayload(Request $request, string $type): array
    {
        $list = function (string $key) use ($request): array {
            return array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                (array) $request->input($key, [])
            ), fn ($v) => $v !== ''));
        };

        $listWithCorrect = function (string $listKey, string $correctKey = 'correct_index') use ($request): array {
            $raw = array_values((array) $request->input($listKey, []));
            $picked = (int) $request->input($correctKey, 0);
            $items = [];
            $correctIndex = 0;
            foreach ($raw as $i => $val) {
                $val = trim((string) $val);
                if ($val === '') {
                    continue;
                }
                if ($i === $picked) {
                    $correctIndex = count($items);
                }
                $items[] = $val;
            }

            return [$items, $correctIndex];
        };

        return match ($type) {
            'who_am_i' => [
                'clues' => $list('clues'),
                'answer' => $request->input('answer'),
            ],
            'case_file', 'multiple_choice' => (function () use ($type, $list, $listWithCorrect) {
                [$options, $correct] = $listWithCorrect('options');

                return [
                    'evidence' => $type === 'case_file' ? $list('evidence') : [],
                    'options' => $options,
                    'correct_index' => $correct,
                ];
            })(),
            'odd_one_out' => (function () use ($listWithCorrect) {
                [$items, $correct] = $listWithCorrect('items');

                return [
                    'items' => $items,
                    'correct_index' => $correct,
                ];
            })(),
            'story_adventure' => [
                'scenario' => $request->input('scenario'),
                'choices' => $this->parseChoices($request),
            ],
            'fact_fiction' => [
                'statement' => $request->input('statement') ?: $request->input('prompt'),
                'correct' => $request->boolean('correct_true'),
            ],
            'silhouette' => [
                'answer' => $request->input('answer'),
                'options' => $list('options'),
            ],
            'match_tracks' => [
                'pairs' => $this->parsePairs($request),
            ],
            'animal_vs_animal' => [
                'animal_a' => $request->input('animal_a'),
                'animal_b' => $request->input('animal_b'),
                'categories' => $this->parseCategories($request),
            ],
            'rank_them' => [
                'criterion' => $request->input('criterion'),
                'items' => $list('items'),
                'correct_order' => $list('items'),
            ],
            default => [],
        };
    }

    protected function parseChoices(Request $request): array
    {
        $texts = array_values((array) $request->input('choice_text', []));
        $outcomes = (array) $request->input('choice_outcome', []);
        $best = (int) $request->input('best_choice', 0);
        $out = [];
        $bestMapped = 0;
        foreach ($texts as $i => $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }
            if ($i === $best) {
                $bestMapped = count($out);
            }
            $out[] = [
                'text' => $text,
                'outcome' => trim((string) ($outcomes[$i] ?? '')),
                'is_best' => false,
            ];
        }
        if ($out !== []) {
            $out[$bestMapped]['is_best'] = true;
        }

        return $out;
    }

    protected function parsePairs(Request $request): array
    {
        $tracks = array_values(array_filter(array_map('trim', (array) $request->input('track_label', []))));
        $animals = (array) $request->input('animal_label', []);
        $out = [];
        foreach ($tracks as $i => $track) {
            $out[] = [
                'track' => $track,
                'animal' => $animals[$i] ?? '',
            ];
        }

        return $out;
    }

    protected function parseCategories(Request $request): array
    {
        $names = array_values(array_filter(array_map('trim', (array) $request->input('category_name', []))));
        $winners = (array) $request->input('category_winner', []);
        $out = [];
        foreach ($names as $i => $name) {
            $out[] = [
                'name' => $name,
                'winner' => in_array($winners[$i] ?? 'a', ['a', 'b'], true) ? $winners[$i] : 'a',
            ];
        }

        return $out;
    }
}
