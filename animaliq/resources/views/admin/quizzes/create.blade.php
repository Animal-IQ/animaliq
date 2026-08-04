@extends('layouts.admin')

@section('title', 'Create Quiz')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold theme-text-primary mb-6">Create quiz</h1>
    <form method="POST" action="{{ route('admin.quizzes.store') }}" enctype="multipart/form-data" class="theme-card rounded-2xl p-6 space-y-4">
        @csrf
        @if ($errors->any())
            <div class="theme-alert-error rounded p-3 text-sm">
                <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @include('admin.quizzes._form')
        <button type="submit" class="theme-btn">Create &amp; add questions</button>
    </form>
</div>
@endsection
