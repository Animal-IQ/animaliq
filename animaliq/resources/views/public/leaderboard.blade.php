@extends('layouts.public')
@section('title', 'Leaderboard – Animal IQ')
@section('content')

@php
$periods = [
    '7d'  => 'Past 7 days',
    '21d' => 'Past 21 days',
    '4w'  => 'Past 4 weeks',
    '3m'  => 'Past 3 months',
    '6m'  => 'Past 6 months',
    'all' => 'All time',
];
$pointInfo = [
    'account_created'         => ['label' => 'Joining Animal IQ',                  'pts' => 5],
    'post_published'          => ['label' => 'Publishing a blog article',           'pts' => 20],
    'research_published'      => ['label' => 'Publishing a research project',       'pts' => 25],
    'blog_view'               => ['label' => 'Reading a blog article',              'pts' => 1],
    'blog_like'               => ['label' => 'Liking a blog post',                  'pts' => 2],
    'blog_bookmark'           => ['label' => 'Bookmarking a blog post',             'pts' => 3],
    'blog_comment'            => ['label' => 'Commenting on a blog post',           'pts' => 5],
    'blog_comment_like'       => ['label' => 'Liking a blog comment',               'pts' => 1],
    'post_received_view'      => ['label' => 'Your article gets a view',            'pts' => 1],
    'post_received_like'      => ['label' => 'Your article gets a like',            'pts' => 2],
    'post_received_bookmark'  => ['label' => 'Your article gets bookmarked',        'pts' => 2],
    'post_received_comment'   => ['label' => 'Your article gets a comment',         'pts' => 3],
    'forum_post'              => ['label' => 'Creating a forum discussion',         'pts' => 10],
    'forum_like'              => ['label' => 'Liking a forum post',                 'pts' => 2],
    'forum_bookmark'          => ['label' => 'Bookmarking a forum post',            'pts' => 3],
    'forum_comment'           => ['label' => 'Commenting in the forum',             'pts' => 5],
    'forum_comment_like'      => ['label' => 'Liking a forum comment',              'pts' => 1],
    'forum_received_like'     => ['label' => 'Your forum post gets a like',         'pts' => 2],
    'forum_received_bookmark' => ['label' => 'Your forum post gets bookmarked',     'pts' => 2],
    'forum_received_comment'  => ['label' => 'Your forum post gets a comment',      'pts' => 3],
    'event_register'          => ['label' => 'Registering for an event',            'pts' => 8],
    'donation'                => ['label' => 'Making a donation',                   'pts' => 15],
    'share'                   => ['label' => 'Sharing content',                     'pts' => 3],
    'quiz_complete'           => ['label' => 'Completing a quiz',                   'pts' => 8],
    'quiz_score'              => ['label' => 'Quiz score bonus (up to 20)',         'pts' => 20],
    'quiz_high_score'         => ['label' => 'Passing a quiz (high score)',         'pts' => 10],
    'quiz_perfect'            => ['label' => 'Perfect quiz score (100%)',           'pts' => 15],
];
@endphp

{{-- Hero --}}
<div class="relative rounded-2xl overflow-hidden mb-8 reveal" style="background:linear-gradient(135deg,var(--accent-orange) 0%,#c45a10 100%);">
    <div class="px-6 py-10 md:py-14 text-white relative z-10">
        <div class="accent-bar" style="background:rgba(255,255,255,0.4);"></div>
        <h1 class="text-3xl md:text-4xl font-bold mb-2">Leaderboard</h1>
        <p class="text-white/80 max-w-xl">Celebrating the most active Animal IQ members. Every action earns points — reading, commenting, discussing, registering for events, and more.</p>
    </div>
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 80% 50%, #fff 0%, transparent 60%);"></div>
</div>

<div class="flex flex-col lg:flex-row gap-6">
    {{-- Main leaderboard --}}
    <div class="flex-1 min-w-0">
        {{-- Period selector --}}
        <div class="flex flex-wrap gap-2 mb-6 reveal">
            @foreach($periods as $key => $label)
                <a href="{{ route('leaderboard', ['period' => $key]) }}"
                   class="px-3 py-1.5 rounded-full text-sm font-medium transition"
                   style="{{ $period === $key ? 'background:var(--accent-orange);color:#fff;' : 'background:var(--bg-warm);color:var(--text-secondary);' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if($rows->isEmpty())
            <div class="theme-card rounded-xl p-10 text-center reveal">
                <p class="theme-text-secondary">No activity recorded for this period yet. Start engaging to appear here!</p>
            </div>
        @else
            {{-- Top 3 podium --}}
            @php $top3 = $rows->take(3); @endphp
            <div class="grid grid-cols-3 gap-3 mb-6 items-end reveal">
                @foreach([1, 0, 2] as $podiumIdx)
                    @if($top3->has($podiumIdx))
                        @php $r = $top3->get($podiumIdx); $rank = $podiumIdx + 1; @endphp
                        <div class="theme-card rounded-2xl p-4 text-center flex flex-col items-center {{ $rank === 1 ? 'order-2' : ($rank === 2 ? 'order-1' : 'order-3') }}"
                             style="{{ $rank === 1 ? 'border:2px solid var(--accent-orange);' : '' }}">
                            @php
                                $medal = $rank === 1 ? ['bg' => '#FF7518', 'text' => '#fff', 'icon' => '1'] : ($rank === 2 ? ['bg' => '#94a3b8', 'text' => '#fff', 'icon' => '2'] : ['bg' => '#cd7c3e', 'text' => '#fff', 'icon' => '3']);
                            @endphp
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-black mb-2 {{ $rank === 1 ? 'w-12 h-12' : '' }}"
                                 style="background:{{ $medal['bg'] }};color:{{ $medal['text'] }};">
                                {{ $medal['icon'] }}
                            </div>
                            @if($r->user->profile_photo)
                                <img src="{{ asset('storage/'.$r->user->profile_photo) }}" class="w-12 h-12 rounded-full object-cover mb-2 ring-2" style="ring-color:{{ $medal['bg'] }}">
                            @else
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mb-2" style="background:var(--bg-warm);color:var(--accent-orange);">
                                    {{ strtoupper(substr($r->user->first_name,0,1)) }}
                                </div>
                            @endif
                            <p class="font-semibold theme-text-primary text-sm leading-tight">{{ $r->user->first_name }}<br>{{ $r->user->last_name }}</p>
                            <p class="text-xs theme-text-secondary mt-0.5">{{ number_format($r->total_points) }} pts</p>
                            @auth @if(auth()->id() === $r->user_id) <span class="text-xs mt-1 px-2 py-0.5 rounded-full" style="background:var(--accent-orange);color:#fff;">You</span> @endif @endauth
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Full table --}}
            <div class="theme-card rounded-xl overflow-hidden reveal">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b theme-border" style="background:var(--bg-warm);">
                            <th class="px-4 py-3 text-left theme-text-secondary font-semibold w-12">Rank</th>
                            <th class="px-4 py-3 text-left theme-text-secondary font-semibold">Member</th>
                            <th class="px-4 py-3 text-right theme-text-secondary font-semibold">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y theme-border">
                        @foreach($rows as $i => $r)
                            @php $rank = $i + 1; $isMe = auth()->check() && auth()->id() === $r->user_id; @endphp
                            <tr class="{{ $isMe ? '' : '' }} transition hover:bg-[var(--bg-warm)]"
                                style="{{ $isMe ? 'background:rgba(255,117,24,0.06);' : '' }}">
                                <td class="px-4 py-3 font-bold {{ $rank <= 3 ? '' : 'theme-text-secondary' }}"
                                    style="{{ $rank <= 3 ? 'color:var(--accent-orange);' : '' }}">
                                    @if($rank === 1) #1
                                    @elseif($rank === 2) #2
                                    @elseif($rank === 3) #3
                                    @else #{{ $rank }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($r->user->profile_photo)
                                            <img src="{{ asset('storage/'.$r->user->profile_photo) }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0" style="background:var(--bg-warm);color:var(--accent-orange);">
                                                {{ strtoupper(substr($r->user->first_name,0,1)) }}
                                            </div>
                                        @endif
                                        <span class="theme-text-primary font-medium">
                                            {{ $r->user->first_name }} {{ $r->user->last_name }}
                                            @if($isMe) <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full" style="background:var(--accent-orange);color:#fff;">You</span> @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold theme-text-primary">{{ number_format($r->total_points) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="lg:w-72 space-y-4 shrink-0">

        {{-- Your stats --}}
        @auth
        <div class="theme-card rounded-xl p-5 reveal">
            <h3 class="font-semibold theme-text-primary mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" style="color:var(--accent-orange)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Your Stats
            </h3>
            @if($myRank)
                <div class="text-center mb-4 p-3 rounded-lg" style="background:var(--bg-warm);">
                    <p class="text-2xl font-black" style="color:var(--accent-orange);">#{{ $myRank }}</p>
                    <p class="text-xs theme-text-secondary">your rank</p>
                </div>
            @endif
            @if($myBreakdown && $myBreakdown->isNotEmpty())
                <div class="space-y-2">
                    @foreach($myBreakdown as $action => $row)
                        <div class="flex items-center justify-between text-sm">
                            <span class="theme-text-secondary">{{ $actionLabels[$action] ?? $action }}</span>
                            <span class="font-semibold theme-text-primary">+{{ $row->pts }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm theme-text-secondary">No activity in this period. Start engaging to earn points!</p>
            @endif
        </div>
        @endauth

        {{-- How points work --}}
        <div class="theme-card rounded-xl p-5 reveal">
            <h3 class="font-semibold theme-text-primary mb-3">How Points Work</h3>
            <div class="space-y-2">
                @foreach($pointInfo as $action => $info)
                    <div class="flex items-center justify-between text-sm">
                        <span class="theme-text-secondary">{{ $info['label'] }}</span>
                        <span class="font-bold shrink-0 ml-2" style="color:var(--accent-orange);">+{{ $info['pts'] }}</span>
                    </div>
                @endforeach
            </div>
            <p class="text-xs theme-text-secondary mt-3 pt-3 border-t theme-border">Rankings update in real time. The top member each month receives a special recognition email.</p>
        </div>

    </div>
</div>
@endsection
