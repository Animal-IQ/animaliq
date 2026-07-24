@extends('layouts.public')

@section('title', $quiz->title)

@section('meta')
@php
    $seoTitle = $quiz->title . ' – Animal IQ Quiz';
    $seoDescription = Str::limit(strip_tags($quiz->description ?? 'Take this wildlife quiz and earn points.'), 160);
    $seoCanonical = route('quizzes.show', $quiz);
    $seoImage = $quiz->banner_image;
@endphp
@include('partials.seo')
@endsection

@section('content')
@if($quiz->banner_image)
    <div class="rounded-2xl overflow-hidden mb-8 -mx-4 md:mx-0 h-56 md:h-72">
        <img src="{{ asset('storage/' . $quiz->banner_image) }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover">
    </div>
@endif

<div class="max-w-3xl mx-auto">
    <header class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold theme-accent uppercase">{{ $quiz->difficulty }} · {{ $quiz->questions_count }} questions</p>
            <h1 class="text-3xl md:text-4xl font-bold theme-text-primary mt-1">{{ $quiz->title }}</h1>
            @if($quiz->description)
                <p class="theme-text-secondary mt-3 leading-relaxed">{{ $quiz->description }}</p>
            @endif
            <ul class="mt-4 text-sm theme-text-secondary space-y-1">
                @if($quiz->duration_minutes)<li>Time limit: {{ $quiz->duration_minutes }} minutes</li>@endif
                @if($quiz->available_from)<li>Opens: {{ $quiz->available_from->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</li>@endif
                @if($quiz->available_until)<li>Closes: {{ $quiz->available_until->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</li>@endif
                <li>Pass mark: {{ $quiz->pass_percentage }}%</li>
                <li>Login {{ $quiz->require_login ? 'required' : 'optional' }} · Retakes {{ $quiz->allow_retake ? 'allowed' : 'not allowed' }}</li>
            </ul>
        </div>
        @include('partials.share-button', ['title' => $quiz->title . ' – Animal IQ Quiz', 'url' => route('quizzes.show', $quiz)])
    </header>

    @if($availability)
        <p class="mb-4 text-sm font-medium {{ $quiz->availabilityStatus() === 'upcoming' ? 'text-amber-600' : 'theme-text-secondary' }}">{{ $availability }}</p>
    @endif

    @if($canAttempt)
        <form method="POST" action="{{ route('quizzes.start', $quiz) }}">
            @csrf
            <button type="submit" class="theme-btn px-8 py-3">Start quiz</button>
        </form>
    @elseif($quiz->require_login && !auth()->check() && $quiz->isAvailableNow())
        <a href="{{ route('login') }}" class="theme-btn inline-block">Log in to start</a>
    @elseif($quiz->availabilityStatus() === 'upcoming')
        <p class="theme-text-secondary">This quiz is not open yet. Check back at the start time above.</p>
    @elseif($quiz->availabilityStatus() === 'ended')
        <p class="theme-text-secondary">This quiz window has ended. You can still review past attempts below if you have any.</p>
    @else
        <p class="theme-text-secondary">This quiz is not available for a new attempt right now.</p>
    @endif

    @if($myAttempts->isNotEmpty())
        <section class="mt-10 pt-8 border-t theme-border">
            <h2 class="text-xl font-bold theme-text-primary mb-4">Your recent attempts</h2>
            <ul class="space-y-2">
                @foreach($myAttempts as $a)
                    <li>
                        <a href="{{ route('quizzes.result', [$quiz, $a]) }}" class="theme-card rounded-xl px-4 py-3 flex justify-between theme-link">
                            <span>{{ $a->completed_at?->format('M j, Y g:i A') }}</span>
                            <span class="font-semibold">{{ number_format($a->percentage, 0) }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
