@php $p = $question->payload ?? []; @endphp
<p class="text-lg font-bold theme-text-primary mb-4">{{ $p['animal_a'] ?? 'A' }} vs {{ $p['animal_b'] ?? 'B' }}</p>
@foreach(($p['categories'] ?? []) as $i => $cat)
    <div class="mb-3">
        <p class="text-sm font-medium mb-1">{{ $cat['name'] ?? 'Category' }}</p>
        <div class="flex gap-2">
            <label class="theme-card rounded-lg px-3 py-2 cursor-pointer text-sm"><input type="radio" name="picks[{{ $i }}]" value="a" required> {{ $p['animal_a'] ?? 'A' }}</label>
            <label class="theme-card rounded-lg px-3 py-2 cursor-pointer text-sm"><input type="radio" name="picks[{{ $i }}]" value="b" required> {{ $p['animal_b'] ?? 'B' }}</label>
        </div>
    </div>
@endforeach
