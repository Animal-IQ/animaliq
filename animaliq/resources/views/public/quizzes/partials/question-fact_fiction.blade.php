@php $p = $question->payload ?? []; @endphp
<p class="text-lg theme-text-primary font-medium mb-4">{{ $p['statement'] ?? $question->prompt }}</p>
<div class="flex flex-wrap gap-3">
    <label class="theme-card rounded-xl px-6 py-3 cursor-pointer font-semibold has-[:checked]:ring-2 has-[:checked]:ring-[var(--accent-orange)]">
        <input type="radio" name="value" value="1" class="sr-only" required> TRUE
    </label>
    <label class="theme-card rounded-xl px-6 py-3 cursor-pointer font-semibold has-[:checked]:ring-2 has-[:checked]:ring-[var(--accent-orange)]">
        <input type="radio" name="value" value="0" class="sr-only" required> FALSE
    </label>
</div>
