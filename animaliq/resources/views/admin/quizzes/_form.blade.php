@php $quiz = $quiz ?? null; @endphp
<div>
    <label class="block text-sm font-medium mb-1">Title *</label>
    <input type="text" name="title" value="{{ old('title', $quiz->title ?? '') }}" class="theme-input w-full" required>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Slug (auto if empty)</label>
    <input type="text" name="slug" value="{{ old('slug', $quiz->slug ?? '') }}" class="theme-input w-full">
</div>
<div>
    <label class="block text-sm font-medium mb-1">Description</label>
    <textarea name="description" rows="3" class="theme-input w-full">{{ old('description', $quiz->description ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Banner image</label>
    <input type="file" name="banner_image" accept="image/*" class="theme-input w-full">
    @if(!empty($quiz?->banner_image))
        <img src="{{ asset('storage/' . $quiz->banner_image) }}" class="mt-2 h-24 rounded-lg object-cover" alt="">
        <label class="inline-flex items-center gap-2 mt-2 text-sm"><input type="checkbox" name="remove_banner" value="1"> Remove banner</label>
    @endif
</div>
<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Difficulty</label>
        <select name="difficulty" class="theme-input w-full">
            @foreach($difficulties as $d)
                <option value="{{ $d }}" @selected(old('difficulty', $quiz->difficulty ?? 'medium') === $d)>{{ ucfirst($d) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="theme-input w-full">
            @foreach(['draft','published','archived'] as $s)
                <option value="{{ $s }}" @selected(old('status', $quiz->status ?? 'draft') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Time limit (minutes)</label>
        <input type="number" name="duration_minutes" min="1" value="{{ old('duration_minutes', $quiz->duration_minutes ?? '') }}" class="theme-input w-full" placeholder="No limit">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Max attempts</label>
        <input type="number" name="max_attempts" min="1" value="{{ old('max_attempts', $quiz->max_attempts ?? '') }}" class="theme-input w-full" placeholder="Unlimited">
    </div>
</div>
<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Available from</label>
        <input type="datetime-local" name="available_from" value="{{ old('available_from', isset($quiz) && $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}" class="theme-input w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Available until</label>
        <input type="datetime-local" name="available_until" value="{{ old('available_until', isset($quiz) && $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}" class="theme-input w-full">
    </div>
</div>
<div class="grid sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Pass %</label>
        <input type="number" name="pass_percentage" min="0" max="100" value="{{ old('pass_percentage', $quiz->pass_percentage ?? 50) }}" class="theme-input w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Complete points</label>
        <input type="number" name="points_complete" value="{{ old('points_complete', $quiz->points_complete ?? 8) }}" class="theme-input w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Perfect bonus</label>
        <input type="number" name="points_perfect_bonus" value="{{ old('points_perfect_bonus', $quiz->points_perfect_bonus ?? 15) }}" class="theme-input w-full">
    </div>
</div>
<div>
    <label class="block text-sm font-medium mb-1">High-score bonus (when pass %)</label>
    <input type="number" name="points_high_score_bonus" value="{{ old('points_high_score_bonus', $quiz->points_high_score_bonus ?? 10) }}" class="theme-input w-full">
</div>
<div class="flex flex-wrap gap-4 text-sm">
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions', $quiz->shuffle_questions ?? false))> Shuffle questions</label>
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="show_explanations" value="1" @checked(old('show_explanations', $quiz->show_explanations ?? true))> Show explanations</label>
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="require_login" value="1" @checked(old('require_login', $quiz->require_login ?? true))> Require login <span class="theme-text-secondary">(recommended / default)</span></label>
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="allow_retake" value="1" @checked(old('allow_retake', $quiz->allow_retake ?? true))> Allow retakes</label>
</div>
