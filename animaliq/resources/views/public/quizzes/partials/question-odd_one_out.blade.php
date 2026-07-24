@php $p = $question->payload ?? []; $items = $p['items'] ?? []; @endphp
<p class="text-sm theme-text-secondary mb-3">Which one doesn't belong?</p>
@foreach($items as $i => $item)
    <label class="flex items-center gap-3 theme-card rounded-xl px-4 py-3 cursor-pointer mb-2">
        <input type="radio" name="option_index" value="{{ $i }}" required>
        <span>{{ $item }}</span>
    </label>
@endforeach
