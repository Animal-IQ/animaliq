@php $p = $question->payload ?? []; $pairs = $p['pairs'] ?? []; $animals = collect($pairs)->pluck('animal')->shuffle()->values(); @endphp
<p class="text-sm theme-text-secondary mb-3">Match each track to the correct animal.</p>
@foreach($pairs as $i => $pair)
    <div class="flex flex-wrap items-center gap-3 mb-3">
        <span class="theme-card rounded-lg px-3 py-2 text-sm font-medium min-w-[8rem]">{{ $pair['track'] ?? 'Track '.($i+1) }}</span>
        <select name="matches[{{ $i }}]" class="theme-input flex-1" required>
            <option value="">Select animal…</option>
            @foreach($animals as $animal)
                <option value="{{ $animal }}">{{ $animal }}</option>
            @endforeach
        </select>
    </div>
@endforeach
