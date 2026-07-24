@extends('layouts.public')

@section('title', 'Wildlife Quizzes')

@section('meta')
@php
    $seoTitle = 'Wildlife Quizzes – Animal IQ';
    $seoDescription = 'Take wildlife quizzes: Who Am I, case files, fact or fiction, silhouettes, match the tracks, and more. Earn points on the leaderboard.';
    $seoCanonical = route('quizzes.index');
@endphp
@include('partials.seo')
@endsection

@section('content')
<section class="theme-bg-warm border-b theme-border -mx-4 px-4 py-12 md:py-16">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold tracking-wider uppercase theme-accent mb-2">Learn by playing</p>
            <h1 class="text-4xl md:text-5xl font-bold theme-text-primary">Wildlife Quizzes</h1>
            <p class="text-lg theme-text-secondary mt-2">Detectives, rangers, and wildlife fans — test what you know and climb the leaderboard.</p>
        </div>
        @include('partials.share-button', ['shareTitle' => 'Wildlife Quizzes – Animal IQ', 'url' => route('quizzes.index')])
    </div>
</section>

<div class="py-12 max-w-6xl mx-auto">
    <form method="GET" class="mb-8 flex flex-col md:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search quizzes..." class="theme-input flex-1">
        <select name="difficulty" class="theme-input">
            <option value="">All levels</option>
            @foreach(['easy','medium','expert'] as $d)
                <option value="{{ $d }}" @selected(request('difficulty') === $d)>{{ ucfirst($d) }}</option>
            @endforeach
        </select>
        <select name="sort" class="theme-input">
            <option value="newest" @selected(request('sort','newest')==='newest')>Newest</option>
            <option value="title" @selected(request('sort')==='title')>Title</option>
            <option value="difficulty" @selected(request('sort')==='difficulty')>Difficulty</option>
        </select>
        <button class="theme-btn">Filter</button>
    </form>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($quizzes as $quiz)
            <article class="theme-card rounded-2xl overflow-hidden flex flex-col hover-lift">
                <a href="{{ route('quizzes.show', $quiz) }}" class="block flex-1">
                    <div class="h-40 bg-[var(--bg-secondary)]">
                        @if($quiz->banner_image)
                            <img src="{{ asset('storage/' . $quiz->banner_image) }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold theme-accent uppercase">{{ $quiz->difficulty }} · {{ $quiz->questions_count }} Qs</p>
                        <h2 class="text-lg font-bold theme-text-primary mt-1">{{ $quiz->title }}</h2>
                        <p class="text-sm theme-text-secondary mt-2 line-clamp-2">{{ $quiz->description }}</p>
                        @if($quiz->duration_minutes)
                            <p class="text-xs theme-text-secondary mt-2">{{ $quiz->duration_minutes }} min</p>
                        @endif
                    </div>
                </a>
                <div class="p-5 pt-0 flex items-center justify-between gap-2">
                    <a href="{{ route('quizzes.show', $quiz) }}" class="theme-btn text-sm">Open</a>
                    @include('partials.share-button', ['shareTitle' => $quiz->title . ' – Animal IQ Quiz', 'url' => route('quizzes.show', $quiz)])
                </div>
            </article>
        @empty
            <div class="col-span-full theme-card rounded-2xl p-12 text-center">
                <p class="theme-text-secondary">No quizzes available right now.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $quizzes->links() }}</div>
</div>
@endsection
