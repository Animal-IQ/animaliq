@extends('layouts.public')

@section('title', 'Animal IQ – Wildlife Education, Conservation & Community in Kenya')

@section('meta')
@php
    $seoTitle       = 'Animal IQ – Wildlife Education, Conservation & Community';
    $seoDescription = 'Animal IQ connects youth and communities with wildlife education, conservation programs, events, and research in Kenya. Join us to protect and celebrate nature.';
    $seoCanonical   = route('home');
    $seoImage       = $seoImage ?? null;
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'        => 'Organization',
                '@id'          => url('/') . '#organization',
                'name'         => 'Animal IQ',
                'url'          => url('/'),
                'logo'         => $seoImage ? asset('storage/' . $seoImage) : url('/favicon.ico'),
                'description'  => 'Wildlife and environmental education organization connecting youth with conservation, programs, events, and research.',
                'email'        => 'info@animaliq.co.ke',
                'sameAs'       => [],
                'contactPoint' => [
                    '@type'       => 'ContactPoint',
                    'contactType' => 'customer support',
                    'email'       => 'info@animaliq.co.ke',
                ],
            ],
            [
                '@type'           => 'WebSite',
                '@id'             => url('/') . '#website',
                'url'             => url('/'),
                'name'            => 'Animal IQ',
                'publisher'       => ['@id' => url('/') . '#organization'],
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => [
                        '@type'       => 'EntryPoint',
                        'urlTemplate' => url('/blog') . '?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ];
@endphp
@include('partials.seo')
@endsection

@section('content')
@push('styles')
<style>
/* Hero sits under absolute header; no top padding offset needed */
</style>
@endpush
    {{-- Full-viewport hero carousel (image under transparent nav) --}}
    <section class="hero-full-width hero-viewport mb-0" id="home-hero">
        @if($slides->isNotEmpty())
            <div class="relative h-full">
                <div id="hero-track" class="flex h-full transition-transform duration-500 ease-out" style="width: {{ $slides->count() * 100 }}vw;">
                    @foreach($slides as $slide)
                        <div class="hero-slide flex-shrink-0 flex items-center justify-center relative bg-[var(--bg-warm)] {{ $slide->image_path ? 'hero-slide--with-image' : '' }}">
                            @if($slide->image_path)
                                <div class="absolute inset-0 z-0">
                                    <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 z-10" style="background: linear-gradient(180deg, rgba(0,0,0,0.45) 0%, rgba(0,0,0,0.25) 40%, rgba(0,0,0,0.55) 100%);"></div>
                                </div>
                            @endif
                            <div class="hero-slide-content relative z-20 px-6 md:px-12 py-16 text-center max-w-4xl mx-auto">
                                <h1 class="text-4xl md:text-6xl font-bold drop-shadow-lg {{ $slide->image_path ? 'text-white' : 'theme-text-primary' }}">{{ $slide->title ?? 'Welcome to Animal IQ' }}</h1>
                                @if($slide->subtitle)
                                    <p class="mt-4 text-lg md:text-2xl drop-shadow-md {{ $slide->image_path ? 'text-white/95' : 'theme-text-secondary' }}">{{ $slide->subtitle }}</p>
                                @endif
                                @if($slide->cta_text && $slide->cta_link)
                                    <div class="flex flex-wrap gap-3 justify-center mt-8">
                                        <a href="{{ $slide->cta_link }}" class="theme-btn px-8 py-3.5 text-base">{{ $slide->cta_text }}</a>
                                        @if($slide->cta_secondary_text && $slide->cta_secondary_link)
                                            <a href="{{ $slide->cta_secondary_link }}" class="theme-btn-outline px-8 py-3.5 text-base {{ $slide->image_path ? 'border-white text-white hover:bg-white/20' : '' }}">{{ $slide->cta_secondary_text }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($slides->count() > 1)
                    <button type="button" class="hero-arrow hero-arrow-prev absolute left-2 md:left-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="Previous slide">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="hero-arrow hero-arrow-next absolute right-2 md:right-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="Next slide">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute bottom-6 left-0 right-0 z-30 flex justify-center gap-2">
                        @foreach($slides as $i => $slide)
                            <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full border-2 border-white/80 bg-white/40 hover:bg-white/70 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--accent-orange)]" aria-label="Go to slide {{ $i + 1 }}" data-index="{{ $i }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="h-full min-h-[520px] flex items-center justify-center theme-bg-warm">
                <div class="p-8 text-center pt-24">
                    <h1 class="text-4xl md:text-6xl font-bold theme-text-primary">The Wild Window</h1>
                    <p class="mt-4 text-lg md:text-xl theme-text-secondary">Connecting youth with wildlife and environmental education.</p>
                </div>
            </div>
        @endif
    </section>

    @if($slides->isNotEmpty() && $slides->count() > 1)
    <script>
    (function() {
        var track = document.getElementById('hero-track');
        if (!track) return;
        var wrapper = track.closest('.hero-full-width');
        var dots = Array.prototype.slice.call(document.querySelectorAll('.hero-dot'));
        var prevBtn = wrapper.querySelector('.hero-arrow-prev');
        var nextBtn = wrapper.querySelector('.hero-arrow-next');
        var slides = Array.prototype.slice.call(track.querySelectorAll('.hero-slide'));
        var realTotal = slides.length;
        if (realTotal < 2) return;

        // Clone first/last for seamless endless loop in one direction
        var firstClone = slides[0].cloneNode(true);
        var lastClone = slides[realTotal - 1].cloneNode(true);
        firstClone.setAttribute('aria-hidden', 'true');
        lastClone.setAttribute('aria-hidden', 'true');
        track.appendChild(firstClone);
        track.insertBefore(lastClone, slides[0]);

        var allSlides = track.querySelectorAll('.hero-slide');
        var trackLen = allSlides.length; // realTotal + 2
        track.style.width = (trackLen * 100) + 'vw';

        // Index in the extended track: 0 = lastClone, 1..realTotal = real slides, trackLen-1 = firstClone
        var current = 1;
        var intervalId = null;
        var animating = false;
        var TRANSITION = 'transform 0.5s ease-out';

        function setTransform(i, withAnim) {
            track.style.transition = withAnim ? TRANSITION : 'none';
            track.style.transform = 'translateX(-' + (i * 100) + 'vw)';
        }

        function updateDots() {
            var realIndex = ((current - 1) % realTotal + realTotal) % realTotal;
            if (current === 0) realIndex = realTotal - 1;
            if (current === trackLen - 1) realIndex = 0;
            dots.forEach(function(d, j) {
                d.classList.toggle('bg-white', j === realIndex);
                d.classList.toggle('bg-white/40', j !== realIndex);
            });
        }

        function goTo(i, withAnim) {
            if (withAnim === undefined) withAnim = true;
            current = i;
            setTransform(current, withAnim);
            updateDots();
        }

        function next() {
            if (animating) return;
            animating = true;
            goTo(current + 1, true);
        }

        function prev() {
            if (animating) return;
            animating = true;
            goTo(current - 1, true);
        }

        track.addEventListener('transitionend', function(e) {
            if (e.propertyName !== 'transform') return;
            // After sliding onto the first-clone at the end → jump to real first (no animation)
            if (current === trackLen - 1) {
                goTo(1, false);
            }
            // After sliding onto the last-clone at the start → jump to real last (no animation)
            if (current === 0) {
                goTo(realTotal, false);
            }
            // Force reflow so next transition works after disabling transition
            void track.offsetWidth;
            track.style.transition = TRANSITION;
            animating = false;
        });

        function startAuto() {
            if (intervalId) clearInterval(intervalId);
            intervalId = setInterval(next, 5000);
        }

        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() {
                if (animating) return;
                animating = true;
                goTo(i + 1, true);
                startAuto();
            });
        });
        if (prevBtn) prevBtn.addEventListener('click', function() { prev(); startAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', function() { next(); startAuto(); });

        // Start on real first slide (index 1 after prepended clone)
        goTo(1, false);
        void track.offsetWidth;
        track.style.transition = TRANSITION;
        startAuto();

        wrapper.addEventListener('mouseenter', function() {
            if (intervalId) { clearInterval(intervalId); intervalId = null; }
        });
        wrapper.addEventListener('mouseleave', function() { startAuto(); });
    })();
    </script>
    @endif

    {{-- Intro: mission / teaser --}}
    <section class="py-12 md:py-16 theme-bg-secondary -mx-4 px-4 md:rounded-2xl md:mx-0 reveal">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-4">What we do</h2>
            <p class="text-lg md:text-xl theme-text-secondary leading-relaxed">
                {{ $missionTeaser ?: $mission }}
            </p>
            @if($vision)
                <p class="mt-6 text-base theme-text-secondary max-w-2xl mx-auto">{{ Str::limit($vision, 160) }}</p>
            @endif
            @if(isset($founderStory) && $founderStory)
                <div class="mt-8 pt-8 border-t theme-border text-left">
                    <h3 class="text-xl font-bold theme-text-primary mb-3 text-center">Our Founder Story</h3>
                    <div class="prose max-w-none theme-text-secondary mx-auto text-center">
                        {!! nl2br(e($founderStory)) !!}
                    </div>
                </div>
            @endif
            <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
                <a href="{{ route('about') }}" class="theme-link font-semibold">Learn more about us →</a>
            </div>
        </div>
    </section>

    {{-- Impact stats – one row on desktop, equal width --}}
    <section class="py-12 md:py-16">
        <h2 class="text-2xl md:text-3xl font-bold theme-text-primary text-center mb-10 reveal">Our impact</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6 px-4 max-w-[1400px] mx-auto">
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--accent-orange)] reveal reveal-delay-1">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($activePrograms) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Active programs</p>
            </div>
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--orange-500)] reveal reveal-delay-2">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($membersActive) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Members active</p>
            </div>
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--orange-600)] reveal reveal-delay-3">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($eventsHosted) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Events Conducted</p>
            </div>
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--orange-700)] reveal reveal-delay-4">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($researchConducted) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Research Conducted</p>
            </div>
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--orange-400)] reveal reveal-delay-5">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($merchandiseCount) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Merchandise</p>
            </div>
            <div class="theme-card rounded-2xl p-4 lg:p-6 text-center hover-lift border-l-4 border-[var(--orange-300)] reveal reveal-delay-6">
                <p class="text-3xl md:text-4xl font-bold theme-accent tabular-nums stat-number">{{ number_format($publishedArticles) }}</p>
                <p class="text-xs md:text-sm theme-text-secondary mt-1">Published Articles</p>
            </div>
        </div>
    </section>

    {{-- Animal Awareness Day (shows on the day of celebration) --}}
    @if(isset($todayAwarenessDay) && $todayAwarenessDay)
    <section class="py-10 md:py-14 -mx-4 px-4 md:rounded-2xl md:mx-0 mb-10 reveal" style="background:linear-gradient(135deg, var(--bg-warm) 0%, var(--bg-secondary) 100%);border:1px solid var(--accent-orange);">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-2 mb-4 justify-center">
                <svg class="w-5 h-5 theme-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-bold uppercase tracking-widest theme-accent">Today's Awareness Day</span>
            </div>
            <div class="flex flex-col md:flex-row gap-6 items-center">
                @if($todayAwarenessDay->image)
                <div class="md:w-2/5 flex-shrink-0 h-56 rounded-2xl overflow-hidden">
                    <img src="{{ asset('storage/' . $todayAwarenessDay->image) }}" alt="{{ $todayAwarenessDay->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                <div class="flex-1">
                    <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-3">{{ $todayAwarenessDay->title }}</h2>
                    @if($todayAwarenessDay->body)
                    <p class="theme-text-secondary leading-relaxed mb-4">{{ Str::limit($todayAwarenessDay->body, 250) }}</p>
                    @endif
                    <a href="{{ route('awareness-days.index') }}" class="theme-btn text-sm inline-block">See all awareness days →</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Core programs --}}
    <section class="py-12 md:py-16 theme-bg-warm -mx-4 px-4 md:rounded-2xl md:mx-0">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-2 animate-fade-in-up text-center">Core programs</h2>
            <p class="theme-text-secondary mb-8 max-w-2xl animate-fade-in-up animate-delay-1 text-center mx-auto">Education, youth engagement, and conservation at the heart of what we do.</p>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 stagger-children">
                @forelse($programs as $program)
                    @php $img = $program->image ?? $program->events->first()?->banner_image; @endphp
                    <a href="{{ route('programs.show', $program) }}" class="block theme-card rounded-2xl overflow-hidden hover-lift group">
                        <div class="h-40 bg-[var(--bg-secondary)] overflow-hidden img-zoom">
                            @if($img)
                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $program->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center theme-text-secondary"><svg class="w-16 h-16 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div>
                            @endif
                        </div>
                        <div class="p-5">
                            @if($program->department)
                                <p class="text-xs font-semibold theme-accent uppercase tracking-wide mb-1">{{ $program->department->name }}</p>
                            @endif
                            <h3 class="font-bold text-lg theme-text-primary group-hover:theme-accent transition">{{ $program->title }}</h3>
                            <p class="text-sm theme-text-secondary mt-2 line-clamp-2">{{ Str::limit($program->description, 100) }}</p>
                            <span class="inline-block mt-3 theme-link font-medium text-sm">Learn more →</span>
                        </div>
                    </a>
                @empty
                    <p class="theme-text-secondary col-span-full">Programs will be listed here.</p>
                @endforelse
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('programs.index') }}" class="theme-btn-outline px-6 py-2">View all programs</a>
            </div>
        </div>
    </section>

    {{-- Upcoming events --}}
    <section class="py-12 md:py-16">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-2 text-center">Upcoming events</h2>
            <p class="theme-text-secondary mb-8 text-center mx-auto">Join workshops, field trips, and community activities.</p>
            @if(isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
                <div class="grid md:grid-cols-3 gap-6 stagger-children">
                    @foreach($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event) }}" class="block theme-card rounded-2xl overflow-hidden hover-lift group">
                            <div class="h-36 bg-[var(--bg-secondary)] overflow-hidden img-zoom">
                                @if($event->banner_image)
                                    <img src="{{ asset('storage/' . $event->banner_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center theme-text-secondary"><svg class="w-14 h-14 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-sm theme-accent font-medium">{{ $event->start_datetime?->format('M j, Y') }}</p>
                                <h3 class="font-bold theme-text-primary group-hover:theme-accent transition">{{ $event->title }}</h3>
                                @if($event->program)
                                    <p class="text-xs theme-text-secondary mt-1">{{ $event->program->title }}</p>
                                @endif
                                <span class="inline-block mt-2 theme-link text-sm font-medium">@include('partials.event-view-label', ['event' => $event]) →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8 text-center">
                    <a href="{{ route('events.index') }}" class="theme-btn-outline px-6 py-2">See all events</a>
                </div>
            @else
                <div class="theme-card rounded-2xl p-8 text-center">
                    <p class="theme-text-secondary">No upcoming events right now. Check back soon.</p>
                    <a href="{{ route('events.index') }}" class="inline-block mt-4 theme-link font-medium">Browse events</a>
                </div>
            @endif
        </div>
    </section>

    {{-- Latest from blog --}}
    @if(isset($recentPosts) && $recentPosts->isNotEmpty())
    <section class="py-12 md:py-16 theme-bg-secondary -mx-4 px-4 md:rounded-2xl md:mx-0">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-2 text-center">Latest from the blog</h2>
            <p class="theme-text-secondary mb-8 text-center mx-auto">Stories and updates from our community.</p>
            <div class="grid md:grid-cols-3 gap-6 stagger-children">
                @foreach($recentPosts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="block theme-card rounded-2xl overflow-hidden hover-lift group">
                        <div class="h-44 bg-[var(--bg-primary)] overflow-hidden img-zoom">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center theme-text-secondary"><svg class="w-14 h-14 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold theme-text-primary group-hover:theme-accent transition line-clamp-2">{{ $post->title }}</h3>
                            <p class="text-sm theme-text-secondary mt-1">{{ $post->author->first_name }} {{ $post->author->last_name }} · {{ $post->published_at?->format('M j, Y') }}</p>
                            @if($post->content)
                                <p class="text-sm theme-text-secondary mt-2 line-clamp-2">{{ Str::limit(strip_tags($post->content), 80) }}</p>
                            @endif
                            <span class="inline-block mt-2 theme-link text-sm font-medium">Read more →</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('blog.index') }}" class="theme-btn-outline px-6 py-2">View all posts</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Latest research --}}
    @if(isset($latestResearch) && $latestResearch->isNotEmpty())
    <section class="py-12 md:py-16">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-2 text-center">Research & knowledge hub</h2>
            <p class="theme-text-secondary mb-8 text-center mx-auto">Discover our latest findings and conservation studies.</p>
            <div class="grid md:grid-cols-3 gap-6 stagger-children">
                @foreach($latestResearch as $research)
                    <a href="{{ route('research.show', $research) }}" class="block theme-card rounded-2xl overflow-hidden hover-lift group">
                        <div class="h-44 bg-[var(--bg-primary)] overflow-hidden img-zoom">
                            @if($research->banner_image)
                                <img src="{{ asset('storage/' . $research->banner_image) }}" alt="{{ $research->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center theme-text-secondary"><svg class="w-14 h-14 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($research->department)
                                <p class="text-xs font-semibold theme-accent uppercase tracking-wide mb-1">{{ $research->department->name }}</p>
                            @endif
                            <h3 class="font-bold theme-text-primary group-hover:theme-accent transition line-clamp-2">{{ $research->title }}</h3>
                            @if($research->summary)
                                <p class="text-sm theme-text-secondary mt-2 line-clamp-2">{{ Str::limit($research->summary, 80) }}</p>
                            @endif
                            <span class="inline-block mt-2 theme-link text-sm font-medium">Explore research →</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('research.index') }}" class="theme-btn-outline px-6 py-2">View all research</a>
            </div>
        </div>
    </section>
    @endif

    {{-- Main CTAs --}}
    <section class="py-12 md:py-16 theme-bg-warm -mx-4 px-4 md:rounded-2xl md:mx-0">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold theme-text-primary mb-4 animate-fade-in-up">Get involved</h2>
            <p class="theme-text-secondary mb-8 animate-fade-in-up animate-delay-1">Join the community, attend events, or support our mission.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="theme-btn px-8 py-3">Join the community</a>
                <a href="{{ route('events.index') }}" class="theme-btn-outline px-8 py-3">Attend an event</a>
                <a href="{{ route('donations.index') }}" class="theme-btn-outline px-8 py-3 border-2 border-[var(--accent-orange)]">Support the mission</a>
            </div>
        </div>
    </section>
    @if(isset($todayAwarenessDay) && $todayAwarenessDay)
    {{-- Awareness Day Modal --}}
    <div id="awareness-popup" role="dialog" aria-modal="true" aria-label="Today's Awareness Day"
        style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;padding:1.25rem;backdrop-filter:blur(3px);">
        <div id="awareness-popup-box"
            style="position:relative;width:100%;max-width:480px;background:var(--bg-primary);border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,0.35);overflow:hidden;animation:popupIn 0.3s ease;">
            {{-- Close button --}}
            <button id="awareness-popup-close"
                style="position:absolute;top:0.75rem;right:0.75rem;z-index:10;width:2rem;height:2rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);border:none;cursor:pointer;color:#fff;transition:background 0.15s;"
                aria-label="Close"
                onmouseover="this.style.background='rgba(0,0,0,0.6)'"
                onmouseout="this.style.background='rgba(0,0,0,0.35)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            @if($todayAwarenessDay->image)
            <div style="height:220px;overflow:hidden;">
                <img src="{{ asset('storage/' . $todayAwarenessDay->image) }}" alt="{{ $todayAwarenessDay->title }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @endif
            <div style="padding:1.75rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.75rem;">
                    <svg style="width:1rem;height:1rem;color:var(--accent-orange);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span style="font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--accent-orange);">Today's Awareness Day</span>
                </div>
                <h3 style="font-size:1.35rem;font-weight:700;color:var(--text-primary);margin-bottom:0.6rem;line-height:1.3;">{{ $todayAwarenessDay->title }}</h3>
                @if($todayAwarenessDay->body)
                <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:1.25rem;">{{ Str::limit($todayAwarenessDay->body, 200) }}</p>
                @endif
                <a href="{{ route('awareness-days.index') }}" class="theme-btn" style="display:block;text-align:center;font-size:0.9rem;">Learn More →</a>
                <button id="awareness-popup-dismiss" style="display:block;width:100%;margin-top:0.75rem;background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:0.8rem;padding:0.25rem;">Dismiss</button>
            </div>
        </div>
    </div>
    <style>
    @keyframes popupIn {
        from { opacity:0; transform:scale(0.93) translateY(12px); }
        to   { opacity:1; transform:scale(1)   translateY(0); }
    }
    #awareness-popup { overflow-y:auto; }
    body.popup-open { overflow:hidden; }
    </style>
    <script>
    (function() {
        var key = 'awareness_popup_{{ $todayAwarenessDay->id }}_{{ now()->format('Y-m-d') }}';
        var popup = document.getElementById('awareness-popup');

        function openPopup() {
            popup.style.display = 'flex';
            document.body.classList.add('popup-open');
            document.getElementById('awareness-popup-close').focus();
        }
        function closePopup() {
            popup.style.display = 'none';
            document.body.classList.remove('popup-open');
            sessionStorage.setItem(key, '1');
        }

        if (!sessionStorage.getItem(key) && popup) {
            setTimeout(openPopup, 1500);
        }

        document.getElementById('awareness-popup-close').addEventListener('click', closePopup);
        document.getElementById('awareness-popup-dismiss').addEventListener('click', closePopup);

        // Close on backdrop click (not on the card itself)
        popup.addEventListener('click', function(e) {
            if (e.target === popup) closePopup();
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popup.style.display === 'flex') closePopup();
        });
    })();
    </script>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    if (target.classList.contains('counted')) return;
                    target.classList.add('counted');
                    const finalValStr = target.innerText.replace(/,/g, '');
                    const finalVal = parseInt(finalValStr, 10) || 0;
                    
                    if (finalVal === 0) return;
                    
                    target.innerText = '0';
                    let startVal = 0;
                    const duration = 2000;
                    const stepTime = Math.max(Math.floor(duration / finalVal), 20);
                    const steps = duration / stepTime;
                    const increment = finalVal / steps;

                    const timer = setInterval(() => {
                        startVal += increment;
                        if (startVal >= finalVal) {
                            target.innerText = Number(finalVal).toLocaleString();
                            clearInterval(timer);
                        } else {
                            target.innerText = Math.floor(startVal).toLocaleString();
                        }
                    }, stepTime);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-number').forEach(el => observer.observe(el));
    });
    </script>
@endsection
