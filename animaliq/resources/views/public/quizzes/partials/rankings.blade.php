@php
    $rankings = $rankings ?? collect();
    $myRank = $myRank ?? null;
    $highlightUserId = $highlightUserId ?? auth()->id();
@endphp
<section class="{{ $sectionClass ?? 'mt-10 pt-8 border-t theme-border' }}">
    <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
        <div>
            <h2 class="text-xl font-bold theme-text-primary">{{ $heading ?? 'Quiz rankings' }}</h2>
            <p class="text-sm theme-text-secondary mt-1">Best score per person · higher % wins, then score, then faster time</p>
        </div>
        @if($myRank)
            <p class="text-sm font-semibold theme-accent">Your best rank: #{{ $myRank }}</p>
        @endif
    </div>

    @if($rankings->isEmpty())
        <div class="theme-card rounded-xl p-6 text-center">
            <p class="theme-text-secondary text-sm">No completed attempts yet. Be the first on the board!</p>
        </div>
    @else
        <ol class="space-y-2">
            @foreach($rankings as $row)
                @php
                    $u = $row->user;
                    $isMe = $highlightUserId && (int) $row->user_id === (int) $highlightUserId;
                    $name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: 'Player';
                @endphp
                <li class="theme-card rounded-xl px-4 py-3 flex flex-wrap items-center gap-3 {{ $isMe ? 'ring-2 ring-[var(--accent-orange)]' : '' }}">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0
                        {{ $row->rank === 1 ? 'theme-accent' : 'theme-bg-warm theme-text-primary' }}">
                        {{ $row->rank }}
                    </span>
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        @if(!empty($u?->profile_photo))
                            <img src="{{ asset('storage/' . $u->profile_photo) }}" alt="" class="w-9 h-9 rounded-full object-cover shrink-0">
                        @else
                            <span class="w-9 h-9 rounded-full theme-bg-warm flex items-center justify-center text-sm font-semibold shrink-0">
                                {{ strtoupper(substr($u->first_name ?? 'P', 0, 1)) }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <p class="font-medium theme-text-primary truncate">{{ $name }}@if($isMe) <span class="text-xs theme-accent">(you)</span>@endif</p>
                            <p class="text-xs theme-text-secondary">
                                @if($row->completed_at){{ $row->completed_at->format('M j, Y') }}@endif
                                @if($row->time_spent_seconds)
                                    · {{ gmdate('i:s', $row->time_spent_seconds) }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-bold theme-accent tabular-nums">{{ number_format((float) $row->percentage, 0) }}%</p>
                        <p class="text-xs theme-text-secondary">{{ $row->score }}/{{ $row->max_score }} pts</p>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</section>
