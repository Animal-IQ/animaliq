@php $p = $question->payload ?? []; @endphp
@if(!empty($p['clues']))
    <ul class="list-disc pl-5 space-y-1 theme-text-secondary mb-4">
        @foreach($p['clues'] as $clue)
            <li>{{ $clue }}</li>
        @endforeach
    </ul>
@endif
<label class="block text-sm font-medium mb-1">Who am I?</label>
<input type="text" name="text" class="theme-input w-full" required placeholder="Your answer">
