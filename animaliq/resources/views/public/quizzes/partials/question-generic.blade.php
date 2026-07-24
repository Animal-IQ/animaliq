@php $p = $question->payload ?? []; @endphp
@if(!empty($p['options']))
    @foreach($p['options'] as $i => $opt)
        <label class="flex items-center gap-3 theme-card rounded-xl px-4 py-3 cursor-pointer">
            <input type="radio" name="option_index" value="{{ $i }}" required>
            <span>{{ $opt }}</span>
        </label>
    @endforeach
@else
    <input type="text" name="text" class="theme-input w-full" required>
@endif
