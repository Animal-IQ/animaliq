<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\UserPoint;
use App\Services\QuizScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function __construct(protected QuizScoringService $scoring) {}

    public function index(Request $request)
    {
        $q = Quiz::availableNow()->withCount('questions');
        if ($search = $request->get('q')) {
            $q->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($diff = $request->get('difficulty')) {
            $q->where('difficulty', $diff);
        }
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'title' => $q->orderBy('title'),
            'difficulty' => $q->orderByRaw("FIELD(difficulty,'easy','medium','expert')"),
            default => $q->latest(),
        };
        $quizzes = $q->paginate(9)->withQueryString();

        return view('public.quizzes.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        abort_unless($quiz->status === 'published', 404);
        $quiz->loadCount('questions');
        $user = auth()->user();
        $canAttempt = $quiz->canUserAttempt($user);
        $myAttempts = $user
            ? $quiz->attempts()->where('user_id', $user->id)->where('status', 'completed')->latest()->take(5)->get()
            : collect();

        return view('public.quizzes.show', compact('quiz', 'canAttempt', 'myAttempts'));
    }

    public function start(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->isAvailableNow(), 404);
        $user = $request->user();
        if (! $quiz->canUserAttempt($user)) {
            return redirect()->route('quizzes.show', $quiz)
                ->with('error', $quiz->require_login && ! $user
                    ? 'Please log in to take this quiz.'
                    : 'You cannot attempt this quiz right now.');
        }

        $guestToken = null;
        if (! $user) {
            $guestToken = $request->session()->get('quiz_guest_token') ?: Str::random(40);
            $request->session()->put('quiz_guest_token', $guestToken);
        }

        $attempt = $this->scoring->startAttempt($quiz, $user?->id, $guestToken);

        return redirect()->route('quizzes.play', [$quiz, $attempt]);
    }

    public function play(Quiz $quiz, QuizAttempt $attempt)
    {
        abort_unless($attempt->quiz_id === $quiz->id, 404);
        $this->authorizeAttempt($attempt);

        if ($attempt->isTimedOut()) {
            $this->scoring->finalize($attempt, 'timed_out');

            return redirect()->route('quizzes.result', [$quiz, $attempt])
                ->with('error', 'Time ran out on this quiz.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quizzes.result', [$quiz, $attempt]);
        }

        $order = $attempt->question_order ?: $quiz->questions()->pluck('id')->all();
        $questions = QuizQuestion::whereIn('id', $order)->get()->sortBy(fn ($q) => array_search($q->id, $order))->values();
        $answerRows = $attempt->answers()->get();
        $answered = $answerRows->pluck('quiz_question_id')->all();
        $answersByQuestion = $answerRows->keyBy('quiz_question_id');
        $secondsRemaining = $attempt->secondsRemaining();

        return view('public.quizzes.play', compact(
            'quiz',
            'attempt',
            'questions',
            'answered',
            'answersByQuestion',
            'secondsRemaining'
        ));
    }

    public function answer(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        abort_unless($attempt->quiz_id === $quiz->id, 404);
        $this->authorizeAttempt($attempt);

        $data = $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'nullable',
            'return_q' => 'nullable|integer|min:0',
        ]);

        $question = QuizQuestion::where('quiz_id', $quiz->id)->findOrFail($data['question_id']);
        $answerPayload = $request->input('answer', []);
        if (! is_array($answerPayload)) {
            $answerPayload = ['value' => $answerPayload];
        }

        // Normalize common form fields into answer payload
        $answerPayload = array_merge($answerPayload, array_filter([
            'text' => $request->input('text'),
            'option_index' => $request->has('option_index') ? (int) $request->input('option_index') : null,
            'value' => $request->has('value') ? $request->boolean('value') : ($answerPayload['value'] ?? null),
            'choice_index' => $request->has('choice_index') ? (int) $request->input('choice_index') : null,
            'matches' => $request->input('matches'),
            'picks' => $request->input('picks'),
            'order' => $request->input('order'),
        ], fn ($v) => $v !== null));

        try {
            $row = $this->scoring->submitAnswer($attempt, $question, $answerPayload);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->route('quizzes.result', [$quiz, $attempt])->with('error', $e->getMessage());
        }

        $result = $question->grade($answerPayload);
        $order = $attempt->question_order ?: [];
        $total = count($order);
        $answeredIds = $attempt->answers()->pluck('quiz_question_id')->all();
        $answeredCount = count($answeredIds);

        $currentIndex = (int) ($data['return_q'] ?? 0);
        $pos = array_search($question->id, $order, true);
        if ($pos !== false) {
            $currentIndex = (int) $pos;
        }
        $currentIndex = max(0, min($currentIndex, max(0, $total - 1)));

        $nextIndex = $currentIndex;
        for ($i = 1; $i < $total; $i++) {
            $candidate = ($currentIndex + $i) % $total;
            $qid = $order[$candidate] ?? null;
            if ($qid && ! in_array($qid, $answeredIds, true)) {
                $nextIndex = $candidate;
                break;
            }
            if ($i === 1 && $currentIndex < $total - 1) {
                $nextIndex = $currentIndex + 1;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'is_correct' => $row->is_correct,
                'points_earned' => $row->points_earned,
                'feedback' => $quiz->show_explanations ? $result['feedback'] : null,
                'reveal' => $quiz->show_explanations ? $result['correct_reveal'] : null,
                'progress' => ['answered' => $answeredCount, 'total' => $total],
                'next_q' => $nextIndex,
            ]);
        }

        return redirect()
            ->route('quizzes.play', [$quiz, $attempt, 'q' => $nextIndex])
            ->with('success', $row->is_correct ? 'Correct! Saved.' : 'Answer saved.')
            ->with('last_feedback', $quiz->show_explanations ? $result['feedback'] : null);
    }

    public function finish(Quiz $quiz, QuizAttempt $attempt)
    {
        abort_unless($attempt->quiz_id === $quiz->id, 404);
        $this->authorizeAttempt($attempt);
        $this->scoring->finalize($attempt, $attempt->isTimedOut() ? 'timed_out' : 'completed');

        return redirect()->route('quizzes.result', [$quiz, $attempt]);
    }

    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        abort_unless($attempt->quiz_id === $quiz->id, 404);
        $this->authorizeAttempt($attempt);
        $attempt->load(['answers.question']);

        $order = $attempt->question_order ?: $quiz->questions()->pluck('id')->all();
        $questions = QuizQuestion::whereIn('id', $order)
            ->get()
            ->sortBy(fn ($q) => array_search($q->id, $order))
            ->values();
        $answersByQuestion = $attempt->answers->keyBy('quiz_question_id');

        return view('public.quizzes.result', compact('quiz', 'attempt', 'questions', 'answersByQuestion'));
    }

    protected function authorizeAttempt(QuizAttempt $attempt): void
    {
        $user = auth()->user();
        if ($attempt->user_id) {
            abort_unless($user && $user->id === $attempt->user_id, 403);
        } else {
            $token = session('quiz_guest_token');
            abort_unless($token && $token === $attempt->guest_token, 403);
        }
    }
}
