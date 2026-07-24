@extends('layouts.public')

@section('title', 'Playing: ' . $quiz->title)

@section('content')
@php
    $total = $questions->count();
    $answeredSet = array_flip($answered);
    $startIndex = (int) request('q', 0);
    if ($startIndex < 0 || $startIndex >= $total) {
        $startIndex = 0;
    }
@endphp
<div
    class="max-w-3xl mx-auto"
    id="quiz-play"
    data-seconds="{{ $secondsRemaining }}"
    data-total="{{ $total }}"
    data-start="{{ $startIndex }}"
>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold theme-text-primary">{{ $quiz->title }}</h1>
            <p class="text-sm theme-text-secondary mt-0.5">
                <span id="quiz-progress-label">{{ count($answered) }}</span> / {{ $total }} answered
            </p>
        </div>
        <div class="theme-card rounded-xl px-4 py-2 font-mono font-bold theme-accent text-lg tabular-nums" id="quiz-timer">
            @if($secondsRemaining !== null)--:--@else∞@endif
        </div>
    </div>

    @if(session('success') || session('last_feedback'))
        <div class="theme-card rounded-xl px-4 py-3 mb-4 text-sm">
            @if(session('success'))
                <p class="font-medium">{{ session('success') }}</p>
            @endif
            @if(session('last_feedback'))
                <p class="theme-text-secondary mt-1 whitespace-pre-line">{{ session('last_feedback') }}</p>
            @endif
        </div>
    @endif

    {{-- Jump to question --}}
    <div class="flex flex-wrap gap-2 mb-6" id="quiz-dots" role="navigation" aria-label="Question navigator">
        @foreach($questions as $index => $question)
            @php $isDone = isset($answeredSet[$question->id]); @endphp
            <button
                type="button"
                class="quiz-dot w-9 h-9 rounded-lg text-sm font-semibold border theme-border transition {{ $isDone ? 'theme-accent' : 'theme-bg-warm theme-text-primary' }}"
                data-index="{{ $index }}"
                data-answered="{{ $isDone ? '1' : '0' }}"
                aria-label="Question {{ $index + 1 }}{{ $isDone ? ' (answered)' : '' }}"
            >{{ $index + 1 }}</button>
        @endforeach
    </div>

    <p class="text-sm font-semibold theme-accent mb-3" id="quiz-q-counter">Question 1 of {{ $total }}</p>

    @foreach($questions as $index => $question)
        @php
            $done = isset($answeredSet[$question->id]);
            $prevAnswer = $answersByQuestion[$question->id] ?? null;
        @endphp
        <article
            class="quiz-slide theme-card rounded-2xl p-6 {{ $index === $startIndex ? '' : 'hidden' }}"
            data-index="{{ $index }}"
            data-question-id="{{ $question->id }}"
            id="q-{{ $question->id }}"
        >
            <p class="text-xs font-semibold theme-text-secondary uppercase mb-2">{{ $question->typeLabel() }} · {{ $question->points }} pts</p>
            @if($question->prompt)
                <h2 class="text-xl font-bold theme-text-primary mb-3">{{ $question->prompt }}</h2>
            @endif
            @if($question->image_path)
                <img src="{{ asset('storage/' . $question->image_path) }}" alt="" class="w-full max-h-64 object-contain rounded-xl mb-4 {{ $question->type === 'silhouette' ? 'brightness-0' : '' }}">
            @endif

            @if($done)
                <p class="text-sm mb-3 theme-accent font-medium">Answered — you can change it below, or move on.</p>
            @endif

            <form method="POST" action="{{ route('quizzes.answer', [$quiz, $attempt]) }}" class="space-y-4 quiz-answer-form">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="return_q" value="{{ $index }}">
                @include('public.quizzes.partials.question-' . (view()->exists('public.quizzes.partials.question-' . $question->type) ? $question->type : 'generic'), [
                    'question' => $question,
                    'prevAnswer' => $prevAnswer,
                ])
                <button type="submit" class="theme-btn">{{ $done ? 'Update answer' : 'Save answer' }}</button>
            </form>
        </article>
    @endforeach

    <div class="flex flex-wrap items-center justify-between gap-3 mt-6">
        <button type="button" id="quiz-prev" class="theme-btn-outline px-5 py-2.5 disabled:opacity-40" disabled>← Previous</button>
        <button type="button" id="quiz-next" class="theme-btn-outline px-5 py-2.5">Next →</button>
    </div>

    <form method="POST" action="{{ route('quizzes.finish', [$quiz, $attempt]) }}" class="mt-8 pt-6 border-t theme-border" id="quiz-finish-form">
        @csrf
        <p class="text-sm theme-text-secondary mb-3" id="quiz-finish-hint">
            @if(count($answered) >= $total && $total > 0)
                All questions answered. Review anytime, then finish when ready.
            @else
                You can finish early, or keep answering. Unanswered questions score 0.
            @endif
        </p>
        <button type="submit" class="theme-btn w-full sm:w-auto px-8 py-3" onclick="return confirm('Submit this quiz now?')">Finish quiz</button>
    </form>
</div>

<script>
(function() {
    var wrap = document.getElementById('quiz-play');
    if (!wrap) return;
    var total = parseInt(wrap.getAttribute('data-total') || '0', 10);
    var index = parseInt(wrap.getAttribute('data-start') || '0', 10);
    var slides = Array.prototype.slice.call(document.querySelectorAll('.quiz-slide'));
    var dots = Array.prototype.slice.call(document.querySelectorAll('.quiz-dot'));
    var prevBtn = document.getElementById('quiz-prev');
    var nextBtn = document.getElementById('quiz-next');
    var counter = document.getElementById('quiz-q-counter');

    function show(i) {
        if (i < 0) i = 0;
        if (i >= total) i = total - 1;
        index = i;
        slides.forEach(function(slide) {
            var on = parseInt(slide.getAttribute('data-index'), 10) === index;
            slide.classList.toggle('hidden', !on);
        });
        dots.forEach(function(dot) {
            var on = parseInt(dot.getAttribute('data-index'), 10) === index;
            dot.classList.toggle('ring-2', on);
            dot.classList.toggle('ring-[var(--accent-orange)]', on);
            dot.setAttribute('aria-current', on ? 'true' : 'false');
        });
        if (counter) counter.textContent = 'Question ' + (index + 1) + ' of ' + total;
        if (prevBtn) prevBtn.disabled = index <= 0;
        if (nextBtn) {
            nextBtn.disabled = index >= total - 1;
            nextBtn.textContent = index >= total - 1 ? 'Last question' : 'Next →';
        }
        // Keep return_q in sync for the visible form
        var active = slides.find(function(s) { return parseInt(s.getAttribute('data-index'), 10) === index; });
        if (active) {
            var ret = active.querySelector('input[name="return_q"]');
            if (ret) ret.value = String(index);
        }
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('q', String(index));
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { show(index - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { if (index < total - 1) show(index + 1); });
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            show(parseInt(dot.getAttribute('data-index'), 10));
        });
    });

    // Keyboard: left/right
    document.addEventListener('keydown', function(e) {
        if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT')) return;
        if (e.key === 'ArrowLeft') show(index - 1);
        if (e.key === 'ArrowRight' && index < total - 1) show(index + 1);
    });

    show(index);

    // Timer (only when quiz has a duration)
    var el = document.getElementById('quiz-timer');
    var rawSeconds = wrap.getAttribute('data-seconds');
    var left = rawSeconds === '' || rawSeconds === null ? NaN : parseInt(rawSeconds, 10);
    if (el && !isNaN(left)) {
        function tick() {
            if (left <= 0) {
                el.textContent = '0:00';
                document.getElementById('quiz-finish-form')?.submit();
                return;
            }
            var m = Math.floor(left / 60);
            var s = left % 60;
            el.textContent = m + ':' + (s < 10 ? '0' : '') + s;
            left--;
            setTimeout(tick, 1000);
        }
        tick();
    } else if (el) {
        el.textContent = 'No limit';
        el.classList.remove('font-mono');
    }
})();
</script>
@endsection
