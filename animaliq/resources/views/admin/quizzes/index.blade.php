@extends('layouts.admin')

@section('title', 'Quizzes')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold theme-text-primary">Quizzes</h1>
        <p class="theme-text-secondary text-sm mt-1">Create wildlife quizzes with mixed question types.</p>
    </div>
    <a href="{{ route('admin.quizzes.create') }}" class="theme-btn">New quiz</a>
</div>

@if($quizzes->isEmpty())
    <div class="theme-card rounded-2xl p-10 text-center">
        <p class="theme-text-secondary">No quizzes yet.</p>
        <a href="{{ route('admin.quizzes.create') }}" class="theme-btn inline-block mt-4">Create your first quiz</a>
    </div>
@else
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($quizzes as $quiz)
            <article class="theme-card rounded-2xl p-5 hover-lift">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase theme-accent">{{ $quiz->difficulty }} · {{ $quiz->status }}</p>
                        <h2 class="text-lg font-bold theme-text-primary mt-1">{{ $quiz->title }}</h2>
                        <p class="text-sm theme-text-secondary mt-1">{{ $quiz->questions_count }} questions · {{ $quiz->attempts_count }} attempts</p>
                        @if($quiz->duration_minutes)
                            <p class="text-xs theme-text-secondary mt-1">{{ $quiz->duration_minutes }} min limit</p>
                        @endif
                    </div>
                    @if($quiz->banner_image)
                        <img src="{{ asset('storage/' . $quiz->banner_image) }}" alt="" class="w-16 h-16 rounded-lg object-cover">
                    @endif
                </div>
                <div class="flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="theme-link text-sm font-medium">Edit</a>
                    <a href="{{ route('quizzes.show', $quiz) }}" class="theme-link text-sm font-medium" target="_blank">View public</a>
                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600">Delete</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-6">{{ $quizzes->links() }}</div>
@endif
@endsection
