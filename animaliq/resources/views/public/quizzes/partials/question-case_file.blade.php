@php $p = $question->payload ?? []; @endphp
@if(!empty($p['evidence']))
    <div class="mb-4">
        <p class="text-sm font-semibold theme-text-primary mb-2">Evidence</p>
        <ul class="space-y-1 theme-text-secondary text-sm">
            @foreach($p['evidence'] as $e)
                <li class="theme-bg-warm rounded-lg px-3 py-2">{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
@foreach(($p['options'] ?? []) as $i => $opt)
    <label class="flex items-center gap-3 theme-card rounded-xl px-4 py-3 cursor-pointer">
        <input type="radio" name="option_index" value="{{ $i }}" required>
        <span>{{ $opt }}</span>
    </label>
@endforeach
