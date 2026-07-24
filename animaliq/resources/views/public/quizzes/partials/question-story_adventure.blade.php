@php $p = $question->payload ?? []; @endphp
@if(!empty($p['scenario']))
    <p class="theme-text-secondary mb-4 leading-relaxed">{{ $p['scenario'] }}</p>
@endif
@foreach(($p['choices'] ?? []) as $i => $choice)
    <label class="flex items-start gap-3 theme-card rounded-xl px-4 py-3 cursor-pointer mb-2">
        <input type="radio" name="choice_index" value="{{ $i }}" class="mt-1" required>
        <span>{{ $choice['text'] ?? '' }}</span>
    </label>
@endforeach
