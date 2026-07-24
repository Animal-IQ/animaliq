@extends('layouts.admin')

@section('title', 'Edit Quiz')

@section('content')
<div class="max-w-5xl space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold theme-text-primary">{{ $quiz->title }}</h1>
            <p class="text-sm theme-text-secondary mt-1">Share link: <a href="{{ route('quizzes.show', $quiz) }}" class="theme-link" target="_blank">{{ route('quizzes.show', $quiz) }}</a></p>
        </div>
        <a href="{{ route('admin.quizzes.index') }}" class="theme-btn-outline text-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" enctype="multipart/form-data" class="theme-card rounded-2xl p-6 space-y-4">
        @csrf @method('PUT')
        @include('admin.quizzes._form')
        <button type="submit" class="theme-btn">Save quiz settings</button>
    </form>

    <section class="theme-card rounded-2xl p-6">
        <h2 class="text-xl font-bold theme-text-primary mb-4">Questions ({{ $quiz->questions->count() }})</h2>
        <div class="space-y-3 mb-8">
            @forelse($quiz->questions as $question)
                <div class="border theme-border rounded-xl p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-semibold theme-accent uppercase">{{ $question->typeLabel() }}</span>
                            <p class="font-medium theme-text-primary">{{ $question->prompt ?: Str::limit(json_encode($question->payload), 80) }}</p>
                            <p class="text-xs theme-text-secondary">{{ $question->points }} pts · {{ $question->difficulty ?? $quiz->difficulty }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" onsubmit="return confirm('Remove question?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600">Remove</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="theme-text-secondary text-sm">No questions yet. Add one below — you can mix types freely.</p>
            @endforelse
        </div>

        <h3 class="font-bold theme-text-primary mb-3">Add question</h3>
        <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}" enctype="multipart/form-data" class="space-y-4" id="question-form">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Question type *</label>
                    <select name="type" id="q-type" class="theme-input w-full" required>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Points</label>
                    <input type="number" name="points" value="10" min="1" class="theme-input w-full">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Prompt / question text</label>
                <input type="text" name="prompt" class="theme-input w-full" placeholder="Optional heading for the question">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Image (silhouette / evidence photo)</label>
                <input type="file" name="image" accept="image/*" class="theme-input w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Difficulty (optional override)</label>
                <select name="difficulty" class="theme-input w-full">
                    <option value="">Same as quiz</option>
                    @foreach($difficulties as $d)
                        <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Who Am I --}}
            <div data-type-fields="who_am_i" class="q-fields space-y-3">
                <label class="block text-sm font-medium">Clues</label>
                <div class="dynamic-list space-y-2" data-list="clues" data-min="1">
                    @for($i = 0; $i < 3; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <input type="text" name="clues[]" class="theme-input flex-1" placeholder="Clue {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2" title="Remove">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="clues">+ Add clue</button>
                <label class="block text-sm font-medium">Answer</label>
                <input type="text" name="answer" class="theme-input w-full" placeholder="Leopard">
            </div>

            {{-- Case file --}}
            <div data-type-fields="case_file" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Evidence</label>
                <div class="dynamic-list space-y-2" data-list="evidence" data-min="1">
                    @for($i = 0; $i < 3; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <input type="text" name="evidence[]" class="theme-input flex-1" placeholder="Evidence {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="evidence">+ Add evidence</button>
                <label class="block text-sm font-medium mt-2">Answer options — mark the correct one</label>
                <div class="dynamic-list space-y-2" data-list="case_options" data-min="2" data-pick="correct_index">
                    @for($i = 0; $i < 4; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <label class="flex items-center gap-2 shrink-0 text-sm" title="Correct answer">
                                <input type="radio" name="correct_index" value="{{ $i }}" @checked($i === 0)>
                                <span class="sr-only">Correct</span>
                            </label>
                            <input type="text" name="options[]" class="theme-input flex-1" placeholder="Option {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="case_options">+ Add option</button>
            </div>

            {{-- Multiple choice --}}
            <div data-type-fields="multiple_choice" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Options — mark the correct one</label>
                <div class="dynamic-list space-y-2" data-list="mc_options" data-min="2" data-pick="correct_index">
                    @for($i = 0; $i < 4; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <label class="flex items-center gap-2 shrink-0 text-sm" title="Correct answer">
                                <input type="radio" name="correct_index" value="{{ $i }}" @checked($i === 0)>
                                <span class="sr-only">Correct</span>
                            </label>
                            <input type="text" name="options[]" class="theme-input flex-1" placeholder="Option {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="mc_options">+ Add option</button>
            </div>

            {{-- Odd one out --}}
            <div data-type-fields="odd_one_out" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Items — mark the odd one out</label>
                <div class="dynamic-list space-y-2" data-list="odd_items" data-min="2" data-pick="correct_index">
                    @for($i = 0; $i < 4; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <label class="flex items-center gap-2 shrink-0 text-sm" title="Odd one out">
                                <input type="radio" name="correct_index" value="{{ $i }}" @checked($i === 0)>
                                <span class="sr-only">Odd one</span>
                            </label>
                            <input type="text" name="items[]" class="theme-input flex-1" placeholder="Item {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="odd_items">+ Add item</button>
            </div>

            {{-- Story adventure --}}
            <div data-type-fields="story_adventure" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Scenario</label>
                <textarea name="scenario" rows="3" class="theme-input w-full"></textarea>
                <label class="block text-sm font-medium">Choices — mark the best practice</label>
                <div class="dynamic-list space-y-2" data-list="story_choices" data-min="2" data-pick="best_choice">
                    @for($i = 0; $i < 3; $i++)
                        <div class="dyn-row border theme-border rounded-lg p-3 space-y-2">
                            <div class="flex gap-2 items-center">
                                <label class="flex items-center gap-2 shrink-0 text-sm" title="Best choice">
                                    <input type="radio" name="best_choice" value="{{ $i }}" @checked($i === 0)>
                                    <span>Best</span>
                                </label>
                                <input type="text" name="choice_text[]" class="theme-input flex-1" placeholder="Choice {{ $i + 1 }}">
                                <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                            </div>
                            <textarea name="choice_outcome[]" rows="2" class="theme-input w-full" placeholder="What happens / outcome"></textarea>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="story_choices">+ Add choice</button>
            </div>

            {{-- Fact / fiction --}}
            <div data-type-fields="fact_fiction" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Statement</label>
                <input type="text" name="statement" class="theme-input w-full" placeholder="All snakes are venomous.">
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="correct_true" value="1"> Statement is TRUE (unchecked = FALSE)</label>
            </div>

            {{-- Silhouette --}}
            <div data-type-fields="silhouette" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Answer</label>
                <input type="text" name="answer" class="theme-input w-full" placeholder="Leopard">
                <label class="block text-sm font-medium">Optional multiple-choice options</label>
                <div class="dynamic-list space-y-2" data-list="sil_options" data-min="0">
                    @for($i = 0; $i < 2; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <input type="text" name="options[]" class="theme-input flex-1" placeholder="Option {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="sil_options">+ Add option</button>
            </div>

            {{-- Match tracks --}}
            <div data-type-fields="match_tracks" class="q-fields space-y-3 hidden">
                <p class="text-sm theme-text-secondary">Pair each track with the matching animal.</p>
                <div class="dynamic-list space-y-2" data-list="track_pairs" data-min="2">
                    @for($i = 0; $i < 4; $i++)
                        <div class="dyn-row flex flex-wrap gap-2 items-center">
                            <input type="text" name="track_label[]" class="theme-input flex-1 min-w-[8rem]" placeholder="Track {{ $i + 1 }}">
                            <input type="text" name="animal_label[]" class="theme-input flex-1 min-w-[8rem]" placeholder="Animal {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="track_pairs">+ Add pair</button>
            </div>

            {{-- Animal vs animal --}}
            <div data-type-fields="animal_vs_animal" class="q-fields space-y-3 hidden">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="animal_a" class="theme-input" placeholder="Animal A (e.g. Lion)">
                    <input type="text" name="animal_b" class="theme-input" placeholder="Animal B (e.g. Leopard)">
                </div>
                <p class="text-sm theme-text-secondary">Comparison categories — edit, remove, or add more.</p>
                <div class="dynamic-list space-y-2" data-list="vs_categories" data-min="1">
                    @foreach(['Speed','Bite Force','Climbing','Camouflage','Endurance'] as $cat)
                        <div class="dyn-row flex flex-wrap gap-2 items-center">
                            <input type="text" name="category_name[]" value="{{ $cat }}" class="theme-input flex-1 min-w-[8rem]" placeholder="Category">
                            <select name="category_winner[]" class="theme-input w-28">
                                <option value="a">A wins</option>
                                <option value="b">B wins</option>
                            </select>
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="vs_categories">+ Add category</button>
            </div>

            {{-- Rank them --}}
            <div data-type-fields="rank_them" class="q-fields space-y-3 hidden">
                <label class="block text-sm font-medium">Criterion</label>
                <input type="text" name="criterion" class="theme-input w-full" placeholder="Fastest → Slowest">
                <label class="block text-sm font-medium">Items in correct order (top = first). Players see them scrambled.</label>
                <div class="dynamic-list space-y-2" data-list="rank_items" data-min="2">
                    @for($i = 0; $i < 4; $i++)
                        <div class="dyn-row flex gap-2 items-center">
                            <span class="text-xs theme-text-secondary w-6 shrink-0">{{ $i + 1 }}.</span>
                            <input type="text" name="items[]" class="theme-input flex-1" placeholder="Animal / item {{ $i + 1 }}">
                            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
                        </div>
                    @endfor
                </div>
                <button type="button" class="dyn-add theme-btn-outline text-sm" data-target="rank_items">+ Add item</button>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Explanation (shown after answer)</label>
                <textarea name="explanation" rows="3" class="theme-input w-full"></textarea>
            </div>

            <button type="submit" class="theme-btn">Add question</button>
        </form>
    </section>
</div>

<template id="tpl-clues">
    <div class="dyn-row flex gap-2 items-center">
        <input type="text" name="clues[]" data-name="clues[]" class="theme-input flex-1" placeholder="New clue">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-evidence">
    <div class="dyn-row flex gap-2 items-center">
        <input type="text" name="evidence[]" data-name="evidence[]" class="theme-input flex-1" placeholder="New evidence">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-case_options">
    <div class="dyn-row flex gap-2 items-center">
        <label class="flex items-center gap-2 shrink-0 text-sm" title="Correct answer">
            <input type="radio" name="correct_index" data-name="correct_index" value="0">
            <span class="sr-only">Correct</span>
        </label>
        <input type="text" name="options[]" data-name="options[]" class="theme-input flex-1" placeholder="New option">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-mc_options">
    <div class="dyn-row flex gap-2 items-center">
        <label class="flex items-center gap-2 shrink-0 text-sm" title="Correct answer">
            <input type="radio" name="correct_index" data-name="correct_index" value="0">
            <span class="sr-only">Correct</span>
        </label>
        <input type="text" name="options[]" data-name="options[]" class="theme-input flex-1" placeholder="New option">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-odd_items">
    <div class="dyn-row flex gap-2 items-center">
        <label class="flex items-center gap-2 shrink-0 text-sm" title="Odd one out">
            <input type="radio" name="correct_index" data-name="correct_index" value="0">
            <span class="sr-only">Odd one</span>
        </label>
        <input type="text" name="items[]" data-name="items[]" class="theme-input flex-1" placeholder="New item">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-story_choices">
    <div class="dyn-row border theme-border rounded-lg p-3 space-y-2">
        <div class="flex gap-2 items-center">
            <label class="flex items-center gap-2 shrink-0 text-sm" title="Best choice">
                <input type="radio" name="best_choice" data-name="best_choice" value="0">
                <span>Best</span>
            </label>
            <input type="text" name="choice_text[]" data-name="choice_text[]" class="theme-input flex-1" placeholder="New choice">
            <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
        </div>
        <textarea name="choice_outcome[]" data-name="choice_outcome[]" rows="2" class="theme-input w-full" placeholder="What happens / outcome"></textarea>
    </div>
</template>
<template id="tpl-sil_options">
    <div class="dyn-row flex gap-2 items-center">
        <input type="text" name="options[]" data-name="options[]" class="theme-input flex-1" placeholder="New option">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-track_pairs">
    <div class="dyn-row flex flex-wrap gap-2 items-center">
        <input type="text" name="track_label[]" data-name="track_label[]" class="theme-input flex-1 min-w-[8rem]" placeholder="Track">
        <input type="text" name="animal_label[]" data-name="animal_label[]" class="theme-input flex-1 min-w-[8rem]" placeholder="Animal">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-vs_categories">
    <div class="dyn-row flex flex-wrap gap-2 items-center">
        <input type="text" name="category_name[]" data-name="category_name[]" class="theme-input flex-1 min-w-[8rem]" placeholder="Category">
        <select name="category_winner[]" data-name="category_winner[]" class="theme-input w-28">
            <option value="a">A wins</option>
            <option value="b">B wins</option>
        </select>
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>
<template id="tpl-rank_items">
    <div class="dyn-row flex gap-2 items-center">
        <span class="text-xs theme-text-secondary w-6 shrink-0 dyn-num">.</span>
        <input type="text" name="items[]" data-name="items[]" class="theme-input flex-1" placeholder="New item">
        <button type="button" class="dyn-remove theme-btn-outline text-sm px-3 py-2">&times;</button>
    </div>
</template>

<script>
(function() {
    var sel = document.getElementById('q-type');

    function markNames(root) {
        (root || document).querySelectorAll('.q-fields input, .q-fields textarea, .q-fields select').forEach(function(inp) {
            if (inp.name && !inp.dataset.name) inp.dataset.name = inp.name;
        });
    }

    function reindexPick(list) {
        var pick = list.getAttribute('data-pick');
        if (!pick) return;
        var radios = list.querySelectorAll('input[type=radio][data-name="' + pick + '"], input[type=radio][name="' + pick + '"]');
        radios.forEach(function(r, i) {
            r.value = String(i);
            r.dataset.name = pick;
        });
        list.querySelectorAll('.dyn-num').forEach(function(el, i) {
            el.textContent = (i + 1) + '.';
        });
    }

    function renumberAll() {
        document.querySelectorAll('.dynamic-list').forEach(function(list) {
            reindexPick(list);
            list.querySelectorAll('.dyn-num').forEach(function(el, i) {
                el.textContent = (i + 1) + '.';
            });
        });
    }

    function sync() {
        var t = sel.value;
        document.querySelectorAll('.q-fields').forEach(function(el) {
            var show = el.getAttribute('data-type-fields') === t;
            el.classList.toggle('hidden', !show);
            el.querySelectorAll('input,textarea,select').forEach(function(inp) {
                if (!inp.dataset.name && inp.name) inp.dataset.name = inp.name;
                if (show && inp.dataset.name) inp.setAttribute('name', inp.dataset.name);
                else inp.removeAttribute('name');
            });
        });
        renumberAll();
    }

    markNames();
    sel.addEventListener('change', sync);
    sync();

    document.querySelectorAll('.dyn-add').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key = btn.getAttribute('data-target');
            var list = document.querySelector('.dynamic-list[data-list="' + key + '"]');
            var tpl = document.getElementById('tpl-' + key);
            if (!list || !tpl) return;
            list.appendChild(tpl.content.cloneNode(true));
            markNames(list);
            sync();
            // Ensure newly added radio/name fields get names if this type is visible
            var parentFields = list.closest('.q-fields');
            if (parentFields && !parentFields.classList.contains('hidden')) {
                list.querySelectorAll('input,textarea,select').forEach(function(inp) {
                    if (inp.dataset.name) inp.setAttribute('name', inp.dataset.name);
                });
            }
            reindexPick(list);
            list.querySelectorAll('.dyn-num').forEach(function(el, i) {
                el.textContent = (i + 1) + '.';
            });
        });
    });

    document.getElementById('question-form').addEventListener('click', function(e) {
        var btn = e.target.closest('.dyn-remove');
        if (!btn) return;
        var row = btn.closest('.dyn-row');
        var list = btn.closest('.dynamic-list');
        if (!row || !list) return;
        var min = parseInt(list.getAttribute('data-min') || '1', 10);
        var rows = list.querySelectorAll('.dyn-row');
        if (rows.length <= min) {
            alert('Keep at least ' + min + ' item(s).');
            return;
        }
        var hadChecked = row.querySelector('input[type=radio]:checked');
        row.remove();
        reindexPick(list);
        list.querySelectorAll('.dyn-num').forEach(function(el, i) {
            el.textContent = (i + 1) + '.';
        });
        if (hadChecked) {
            var first = list.querySelector('input[type=radio]');
            if (first) first.checked = true;
        }
    });
})();
</script>
@endsection
