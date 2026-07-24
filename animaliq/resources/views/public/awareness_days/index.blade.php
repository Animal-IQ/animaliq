@extends('layouts.public')

@section('title', 'Animal Awareness Days & Environmental Holidays – Animal IQ Initiative')

@section('meta')
@php
    $seoTitle       = 'Animal Awareness Days & Environmental Holidays – Animal IQ Initiative';
    $seoDescription = 'Explore our calendar of Animal Awareness Days and Environmental Holidays. Learn what each celebration means and how you can participate.';
    $seoCanonical   = route('awareness-days.index');
@endphp
@include('partials.seo')
@endsection

@section('content')
    <section class="theme-bg-warm border-b theme-border -mx-4 px-4 py-12 md:py-16 mb-8">
        <div class="max-w-6xl mx-auto">
            <p class="text-sm font-semibold tracking-wider uppercase theme-accent mb-2 animate-fade-in-up">Wildlife & Environment</p>
            <h1 class="text-4xl md:text-5xl font-bold theme-text-primary animate-fade-in-up animate-delay-1">Awareness Days Calendar</h1>
            <p class="text-lg theme-text-secondary mt-3 max-w-2xl animate-fade-in-up animate-delay-2">Celebrating animal & environmental holidays throughout the year.</p>
            <div class="mt-4 accent-bar"></div>
        </div>
    </section>

    @if($todayDay)
    <div class="max-w-6xl mx-auto mb-10">
        <div class="theme-card rounded-2xl overflow-hidden border-2" style="border-color:var(--accent-orange)">
            <div class="flex flex-col md:flex-row">
                @if($todayDay->image)
                <div class="md:w-2/5 h-56 md:h-auto min-h-[200px] overflow-hidden">
                    <img src="{{ asset('storage/' . $todayDay->image) }}" alt="{{ $todayDay->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                <div class="flex-1 p-6 md:p-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold mb-4" style="background:var(--accent-orange);color:#fff">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Today's Celebration
                    </span>
                    <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-3">{{ $todayDay->title }}</h2>
                    <p class="theme-text-secondary text-base leading-relaxed">{{ $todayDay->body }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold theme-text-primary mb-6 reveal">Full Calendar</h2>
        @forelse($allDays as $day)
        @php
            $isToday = $day->isCelebratingToday();
            $isPast = $day->isPastThisYear();
            $daysUntil = $day->daysUntilNext();
        @endphp
        <article class="theme-card rounded-2xl overflow-hidden mb-6 flex flex-col md:flex-row reveal hover-lift {{ $isToday ? 'ring-2' : '' }}" style="{{ $isToday ? 'ring-color:var(--accent-orange)' : '' }}">
            @if($day->image)
            <div class="md:w-48 h-40 md:h-auto overflow-hidden flex-shrink-0">
                <img src="{{ asset('storage/' . $day->image) }}" alt="{{ $day->title }}" class="w-full h-full object-cover {{ $isPast ? 'opacity-60 grayscale' : '' }}">
            </div>
            @endif
            <div class="flex-1 p-6 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-sm font-semibold theme-accent">{{ $day->celebration_date->format('F j') }}</span>
                    @if($isToday)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background:var(--accent-orange);color:#fff">Today!</span>
                    @elseif($isPast)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Past</span>
                    @elseif($daysUntil <= 30)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--bg-warm);color:var(--accent-orange)">In {{ $daysUntil }} days</span>
                    @endif
                </div>
                <h3 class="text-xl font-bold theme-text-primary mb-2">{{ $day->title }}</h3>
                @if($day->body)
                <p class="theme-text-secondary text-sm leading-relaxed line-clamp-3">{{ $day->body }}</p>
                @endif
            </div>
        </article>
        @empty
        <div class="theme-card rounded-2xl p-12 text-center">
            <p class="theme-text-secondary text-lg">No awareness days scheduled yet.</p>
        </div>
        @endforelse
    </div>
@endsection
