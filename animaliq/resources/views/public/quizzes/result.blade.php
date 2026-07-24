@extends('layouts.public')

@section('title', 'Results: ' . $quiz->title)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center">
        <p class="text-sm font-semibold theme-accent uppercase mb-2">Quiz complete</p>
        <h1 class="text-3xl md:text-4xl font-bold theme-text-primary mb-2">{{ $quiz->title }}</h1>
        <p class="theme-text-secondary mb-8">{{ $attempt->status === 'timed_out' ? 'Time ran out — here is your score.' : 'Great effort!' }}</p>

        <div class="theme-card rounded-2xl p-8 mb-8">
            <p class="text-5xl font-bold theme-accent tabular-nums">{{ number_format($attempt->percentage, 0) }}%</p>
            <p class="theme-text-secondary mt-2">{{ $attempt->score }} / {{ $attempt->max_score }} points</p>
            @if($attempt->time_spent_seconds)
                <p class="text-sm theme-text-secondary mt-1">Time: {{ gmdate('i:s', $attempt->time_spent_seconds) }}</p>
            @endif
            @if($attempt->percentage >= $quiz->pass_percentage)
                <p class="mt-4 font-semibold text-green-600">Passed ({{ $quiz->pass_percentage }}%+)</p>
            @else
                <p class="mt-4 font-semibold theme-text-secondary">Below pass mark ({{ $quiz->pass_percentage }}%)</p>
            @endif
        </div>

        @if(auth()->check())
            <p class="text-sm theme-text-secondary mb-6">Points from this attempt have been added to your leaderboard total.</p>
        @endif

        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <a href="{{ route('quizzes.show', $quiz) }}" class="theme-btn">Back to quiz</a>
            <a href="{{ route('quizzes.index') }}" class="theme-btn-outline">More quizzes</a>
            @include('partials.share-button', ['title' => 'I scored ' . number_format($attempt->percentage, 0) . '% on ' . $quiz->title, 'url' => route('quizzes.show', $quiz)])
        </div>
    </div>

    <section class="text-left">
        <h2 class="text-xl font-bold theme-text-primary mb-4">Answer review</h2>
        @foreach($questions as $index => $question)
            @php
                $ans = $answersByQuestion->get($question->id);
                $yourAnswer = $question->formatUserAnswer($ans?->answer);
                $correctAnswer = $question->formatCorrectAnswer();
                $isCorrect = (bool) ($ans?->is_correct);
                $points = (int) ($ans?->points_earned ?? 0);
            @endphp
            <div class="theme-card rounded-xl p-5 mb-4">
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                    <p class="text-xs font-semibold theme-accent uppercase">Q{{ $index + 1 }} · {{ $question->typeLabel() }}</p>
                    <p class="text-sm font-semibold {{ $ans ? ($isCorrect ? 'text-green-600' : 'text-red-600') : 'theme-text-secondary' }}">
                        @if(! $ans)
                            Skipped · 0 pts
                        @elseif($isCorrect)
                            Correct · +{{ $points }} pts
                        @else
                            Incorrect · +{{ $points }} pts
                        @endif
                    </p>
                </div>
                <p class="theme-text-primary font-medium mb-4">{{ $question->prompt ?: $question->typeLabel() }}</p>

                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg px-3 py-3 {{ $isCorrect ? 'bg-green-500/10' : 'bg-red-500/10' }}">
                        <p class="text-xs font-semibold uppercase theme-text-secondary mb-1">Your answer</p>
                        <p class="theme-text-primary whitespace-pre-line">{{ $yourAnswer }}</p>
                    </div>
                    <div class="rounded-lg px-3 py-3 theme-bg-warm">
                        <p class="text-xs font-semibold uppercase theme-text-secondary mb-1">Correct answer</p>
                        <p class="theme-text-primary whitespace-pre-line">{{ $correctAnswer }}</p>
                    </div>
                </div>

                @if($quiz->show_explanations && $question->explanation)
                    <p class="text-sm theme-text-secondary mt-3 pt-3 border-t theme-border">{{ $question->explanation }}</p>
                @endif
            </div>
        @endforeach
    </section>
</div>
@endsection
