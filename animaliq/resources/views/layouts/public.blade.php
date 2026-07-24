<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    @hasSection('meta')
        @yield('meta')
    @else
        @include('partials.seo')
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    @include('partials.animations')
    @stack('styles')
</head>
<body class="theme-bg-primary theme-text-primary min-h-screen antialiased {{ request()->routeIs('home') ? 'page-home' : '' }}">
    <header id="site-header" class="theme-bg-primary theme-header-border sticky top-0 z-40 {{ request()->routeIs('home') ? 'header-over-hero' : '' }}">
        <nav class="container mx-auto px-4 py-3 md:py-4 flex flex-wrap items-center justify-between gap-2">
            <a href="{{ route('home') }}" class="logo-brand text-xl font-semibold flex items-center gap-2 shrink-0" style="background: linear-gradient(135deg, var(--orange-200), var(--orange-600)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                @php $siteLogo = \App\Models\SiteSetting::getByKey('site_logo'); @endphp
                @if($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 w-8 object-cover rounded-full inline-block" style="-webkit-text-fill-color: initial;">
                @endif
                Animal IQ Initiative
            </a>
            <div class="flex items-center gap-2 md:gap-4">
                @include('partials.theme-toggle')
                {{-- Desktop nav: visible from md up --}}
                <ul class="hidden md:flex flex-wrap items-center gap-4 lg:gap-6">
                    <li>@include('partials.nav-link', ['route' => route('home'), 'label' => 'Home', 'icon' => 'home'])</li>
                    <li>@include('partials.nav-link', ['route' => route('about'), 'label' => 'About', 'icon' => 'about'])</li>
                    <li>@include('partials.nav-link', ['route' => route('programs.index'), 'label' => 'Programs', 'icon' => 'programs'])</li>
                    <li>@include('partials.nav-link', ['route' => route('events.index'), 'label' => 'Events', 'icon' => 'events'])</li>
                    <li>@include('partials.nav-link', ['route' => route('research.index'), 'label' => 'Research', 'icon' => 'research'])</li>
                    <li>@include('partials.nav-link', ['route' => route('blog.index'), 'label' => 'Blog', 'icon' => 'blog'])</li>
                    {{-- "More" dropdown --}}
                    <li class="relative" id="more-dropdown-wrap">
                        @php
                            $moreActive = request()->routeIs('awareness-days.*') || request()->routeIs('podcasts.*') || request()->routeIs('forum.*') || request()->routeIs('quizzes.*') || request()->routeIs('donations.*') || request()->routeIs('store.*');
                        @endphp
                        <button type="button" id="more-dropdown-btn"
                            class="inline-flex items-center gap-1 theme-nav-link"
                            style="background:none;border:none;cursor:pointer;padding:0;{{ $moreActive ? 'color:var(--accent-orange);font-weight:600;' : '' }}"
                            aria-haspopup="true" aria-expanded="false">
                            <span>More</span>
                            <svg class="w-3.5 h-3.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="more-dropdown" class="absolute left-0 mt-1 w-48 rounded-lg border theme-border theme-bg-primary shadow-lg z-50 hidden py-1">
                            <a href="{{ route('awareness-days.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('awareness-days.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('awareness-days.*') ? 'color:var(--accent-orange);' : '' }}">Awareness Days</a>
                            <a href="{{ route('podcasts.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('podcasts.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('podcasts.*') ? 'color:var(--accent-orange);' : '' }}">Podcasts</a>
                            <a href="{{ route('forum.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('forum.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('forum.*') ? 'color:var(--accent-orange);' : '' }}">Forum</a>
                            <a href="{{ route('quizzes.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('quizzes.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('quizzes.*') ? 'color:var(--accent-orange);' : '' }}">Quizzes</a>
                            <div class="border-t theme-border my-1"></div>
                            <a href="{{ route('donations.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium hover:bg-[var(--bg-warm)] {{ request()->routeIs('donations.*') ? 'font-semibold' : '' }}" style="color:var(--accent-orange);">Donate</a>
                            <a href="{{ route('store.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('store.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('store.*') ? 'color:var(--accent-orange);' : '' }}">Store</a>
                            <a href="{{ route('leaderboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] {{ request()->routeIs('leaderboard') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('leaderboard') ? 'color:var(--accent-orange);' : '' }}">Leaderboard</a>
                        </div>
                    </li>
                    @auth
                        <li>
                            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                                <div class="relative" id="dashboard-dropdown-wrap">
                                    <button type="button" id="dashboard-dropdown-btn" class="inline-flex items-center gap-1.5 theme-nav-link" style="background:none;border:none;cursor:pointer;padding:0;" aria-haspopup="true" aria-expanded="false">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        <span @if(request()->is('community/*')) style="color:var(--accent-orange);font-weight:600;" @endif>My Dashboard</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div id="dashboard-dropdown" class="absolute right-0 mt-1 w-48 rounded-lg border theme-border theme-bg-primary shadow-lg z-50 hidden">
                                        <a href="{{ route('community.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] rounded-t-lg @if(request()->is('community/*')) font-semibold @endif" style="@if(request()->is('community/*')) color:var(--accent-orange); @endif">My Dashboard</a>
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm theme-text-primary hover:bg-[var(--bg-warm)] rounded-b-lg @if(request()->is('admin*')) font-semibold @endif" style="@if(request()->is('admin*')) color:var(--accent-orange); @endif">Admin Dashboard</a>
                                    </div>
                                </div>
                            @else
                                @include('partials.nav-link', ['route' => route('community.dashboard'), 'label' => 'My Dashboard', 'icon' => 'dashboard', 'class' => 'theme-link font-medium'])
                            @endif
                        </li>
                        <li>
                            <form method="POST" action="{{ url('/logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="theme-nav-link bg-transparent border-none cursor-pointer p-0 text-left inline-flex items-center gap-1.5">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                        {{-- Bell notification icon --}}
                        <li class="relative" id="bell-wrap">
                            <button id="bell-btn" aria-label="Notifications" class="relative p-1.5 rounded-lg theme-text-secondary hover:text-[var(--accent-orange)] transition" style="background:none;border:none;cursor:pointer;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span id="bell-badge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-white text-[10px] font-bold flex items-center justify-center hidden" style="background:var(--accent-orange);"></span>
                            </button>
                            {{-- Popup --}}
                            <div id="bell-popup" class="hidden absolute right-0 mt-2 w-80 rounded-xl border theme-border theme-bg-primary shadow-2xl z-50" style="top:100%;">
                                <div class="flex items-center justify-between px-4 py-3 border-b theme-border">
                                    <span class="font-semibold theme-text-primary text-sm">Notifications</span>
                                    <button id="bell-mark-all" class="text-xs theme-link">Mark all read</button>
                                </div>
                                <div id="bell-list" class="divide-y" style="border-color:var(--border-color);max-height:340px;overflow-y:auto;"></div>
                                <div class="px-4 py-3 border-t theme-border text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm theme-link font-medium">View all notifications →</a>
                                </div>
                            </div>
                        </li>
                    @else
                        @if (Route::has('login'))
                            <li>@include('partials.nav-link', ['route' => route('login'), 'label' => 'Log in', 'icon' => 'login', 'class' => 'theme-link font-medium'])</li>
                        @endif
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="theme-btn-outline text-sm inline-flex items-center gap-1.5"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg><span>Register</span></a></li>
                        @endif
                    @endauth
                </ul>
                {{-- Mobile hamburger --}}
                <button type="button" class="md:hidden hamburger-btn flex flex-col justify-center gap-1.5 w-10 h-10 p-2 rounded border theme-border theme-bg-secondary" id="mobile-menu-btn" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-nav-panel">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>
        {{-- Mobile nav overlay --}}
        <div class="mobile-menu-overlay md:hidden" id="mobile-menu-overlay" aria-hidden="true"></div>
        {{-- Mobile nav panel (slide-in from right) --}}
        <div class="mobile-nav-panel md:hidden py-6 px-4" id="mobile-nav-panel" role="dialog" aria-label="Navigation menu" aria-modal="true" aria-hidden="true">
            <div class="flex items-center justify-between mb-6 px-2">
                <span class="font-semibold theme-text-primary">Menu</span>
                <button type="button" class="hamburger-btn open w-10 h-10 p-2 rounded border theme-border" id="mobile-menu-close" aria-label="Close menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
            <ul class="space-y-1">
                <li>@include('partials.nav-link', ['route' => route('home'), 'label' => 'Home', 'icon' => 'home', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('about'), 'label' => 'About', 'icon' => 'about', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('programs.index'), 'label' => 'Programs', 'icon' => 'programs', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('events.index'), 'label' => 'Events', 'icon' => 'events', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('research.index'), 'label' => 'Research', 'icon' => 'research', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('blog.index'), 'label' => 'Blog', 'icon' => 'blog', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('awareness-days.index'), 'label' => 'Awareness Days', 'icon' => 'events', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('podcasts.index'), 'label' => 'Podcasts', 'icon' => 'blog', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('forum.index'), 'label' => 'Forum', 'icon' => 'community', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('quizzes.index'), 'label' => 'Quizzes', 'icon' => 'research', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('donations.index'), 'label' => 'Donate', 'icon' => 'donate', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-accent font-medium'])</li>
                <li>@include('partials.nav-link', ['route' => route('store.index'), 'label' => 'Store', 'icon' => 'store', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                <li>@include('partials.nav-link', ['route' => route('leaderboard'), 'label' => 'Leaderboard', 'icon' => 'community', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary'])</li>
                @auth
                    <li>
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                            <a href="{{ route('community.dashboard') }}" class="mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg @if(request()->is('community/*')) font-semibold @endif" style="@if(request()->is('community/*')) color:var(--accent-orange); @endif">My Dashboard</a>
                            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg @if(request()->is('admin*')) font-semibold @endif" style="@if(request()->is('admin*')) color:var(--accent-orange); @endif">Admin Dashboard</a>
                        @else
                            @include('partials.nav-link', ['route' => route('community.dashboard'), 'label' => 'My Dashboard', 'icon' => 'dashboard', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-accent font-medium'])
                        @endif
                    </li>
                    <li>
                        <a href="{{ route('notifications.index') }}" class="mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-text-primary">
                            <span class="relative">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span id="mobile-bell-badge" class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-0.5 rounded-full text-white text-[9px] font-bold flex items-center justify-center hidden" style="background:var(--accent-orange);"></span>
                            </span>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="mobile-nav-link w-full text-left px-4 py-3 rounded-lg theme-text-primary inline-flex items-center gap-1.5">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @else
                    @if (Route::has('login'))
                        <li>@include('partials.nav-link', ['route' => route('login'), 'label' => 'Log in', 'icon' => 'login', 'class' => 'mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-accent font-medium'])</li>
                    @endif
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="mobile-nav-link flex items-center gap-1.5 px-4 py-3 rounded-lg theme-accent font-medium"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg><span>Register</span></a></li>
                    @endif
                @endauth
            </ul>
        </div>
    </header>
    <main class="container mx-auto px-4 theme-text-primary min-w-0 main-enter {{ request()->routeIs('home') ? 'pt-0 pb-6 md:pb-8' : 'py-6 md:py-8' }}">
        @if (session('success'))
            <div class="mb-4 p-4 rounded theme-alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 rounded theme-alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="theme-border border-t mt-12 py-10 theme-bg-secondary footer-enter">
        @php
            $footerSocials = [
                'x'        => ['key' => 'social_x',        'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.736-8.851L1.254 2.25H8.08l4.261 5.636 5.903-5.636Zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
                'instagram' => ['key' => 'social_instagram', 'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>'],
                'facebook'  => ['key' => 'social_facebook',  'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
                'linkedin'  => ['key' => 'social_linkedin',  'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'],
                'whatsapp'  => ['key' => 'social_whatsapp',  'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>'],
                'reddit'    => ['key' => 'social_reddit',    'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>'],
                'tiktok'    => ['key' => 'social_tiktok',    'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>'],
                'youtube'   => ['key' => 'social_youtube',   'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'],
                'telegram'  => ['key' => 'social_telegram',  'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>'],
            ];
            $footerContacts = [
                ['key' => 'contact_primary',   'label' => 'Primary',   'icon' => 'phone'],
                ['key' => 'contact_secondary',  'label' => 'Secondary', 'icon' => 'phone'],
                ['key' => 'contact_whatsapp',   'label' => 'WhatsApp',  'icon' => 'whatsapp'],
                ['key' => 'contact_email1',     'label' => 'Email',     'icon' => 'email'],
                ['key' => 'contact_email2',     'label' => 'Email',     'icon' => 'email'],
            ];
        @endphp
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('home') }}" class="font-semibold text-lg" style="background: linear-gradient(135deg, var(--orange-200), var(--orange-600)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Animal IQ Initiative</a>
                    <p class="text-sm theme-text-secondary mt-2">Education, conservation, and community at the heart of wildlife protection.</p>
                    {{-- Social icons --}}
                    @php $hasSocials = false; foreach ($footerSocials as $sKey => $s) { if (\App\Models\SiteSetting::getByKey($s['key'])) { $hasSocials = true; break; } } @endphp
                    @if($hasSocials)
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($footerSocials as $sKey => $s)
                            @php $url = \App\Models\SiteSetting::getByKey($s['key']); @endphp
                            @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full flex items-center justify-center theme-text-secondary hover:text-[var(--accent-orange)] hover:bg-[var(--bg-warm)] transition" title="{{ ucfirst($sKey) }}" aria-label="{{ ucfirst($sKey) }}">
                                {!! $s['icon'] !!}
                            </a>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                <div>
                    <h3 class="font-semibold theme-text-primary mb-3">Quick links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="theme-nav-link">About</a></li>
                        <li><a href="{{ route('programs.index') }}" class="theme-nav-link">Programs</a></li>
                        <li><a href="{{ route('events.index') }}" class="theme-nav-link">Events</a></li>
                        <li><a href="{{ route('awareness-days.index') }}" class="theme-nav-link">Awareness Days</a></li>
                        <li><a href="{{ route('research.index') }}" class="theme-nav-link">Research</a></li>
                        <li><a href="{{ route('blog.index') }}" class="theme-nav-link">Blog</a></li>
                        <li><a href="{{ route('podcasts.index') }}" class="theme-nav-link">Podcasts</a></li>
                        <li><a href="{{ route('forum.index') }}" class="theme-nav-link">Forum</a></li>
                        <li><a href="{{ route('quizzes.index') }}" class="theme-nav-link">Quizzes</a></li>
                        <li><a href="{{ route('donations.index') }}" class="theme-nav-link">Donate</a></li>
                        <li><a href="{{ route('store.index') }}" class="theme-nav-link">Store</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold theme-text-primary mb-3">Get involved</h3>
                    <p class="text-sm theme-text-secondary mb-3">Support our mission through donations, partnerships, or by joining our community.</p>
                    <a href="{{ route('donations.index') }}" class="theme-btn text-sm inline-block">Donate</a>
                </div>
                {{-- Contact us --}}
                @php
                    $hasContacts = false;
                    foreach ($footerContacts as $c) { if (\App\Models\SiteSetting::getByKey($c['key'])) { $hasContacts = true; break; } }
                @endphp
                @if($hasContacts)
                <div>
                    <h3 class="font-semibold theme-text-primary mb-3">Contact Us</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach($footerContacts as $c)
                            @php $val = \App\Models\SiteSetting::getByKey($c['key']); @endphp
                            @if($val)
                            <li class="flex items-start gap-2 theme-text-secondary">
                                @if($c['icon'] === 'email')
                                <svg class="w-4 h-4 mt-0.5 shrink-0 theme-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:{{ $val }}" class="theme-nav-link break-all">{{ $val }}</a>
                                @elseif($c['icon'] === 'whatsapp')
                                <svg class="w-4 h-4 mt-0.5 shrink-0 theme-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $val) }}" target="_blank" rel="noopener" class="theme-nav-link">{{ $val }}</a>
                                @else
                                <svg class="w-4 h-4 mt-0.5 shrink-0 theme-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <a href="tel:{{ $val }}" class="theme-nav-link">{{ $val }}</a>
                                @endif
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="pt-6 border-t theme-border text-center text-sm theme-text-secondary">
                &copy; {{ date('Y') }} Animal IQ Initiative. All rights reserved.
            </div>
        </div>
    </footer>
    <script>
    (function() {
        var btn = document.getElementById('mobile-menu-btn');
        var closeBtn = document.getElementById('mobile-menu-close');
        var overlay = document.getElementById('mobile-menu-overlay');
        var panel = document.getElementById('mobile-nav-panel');
        function openMenu() {
            if (!panel || !overlay) return;
            panel.classList.add('open');
            overlay.classList.add('open');
            if (btn) { btn.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); btn.setAttribute('aria-label', 'Close menu'); }
            panel.setAttribute('aria-hidden', 'false');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('mobile-nav-open');
        }
        function closeMenu() {
            if (!panel || !overlay) return;
            panel.classList.remove('open');
            overlay.classList.remove('open');
            if (btn) { btn.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); btn.setAttribute('aria-label', 'Open menu'); }
            panel.setAttribute('aria-hidden', 'true');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('mobile-nav-open');
        }
        if (btn) btn.addEventListener('click', function() { (panel && panel.classList.contains('open')) ? closeMenu() : openMenu(); });
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);
        document.querySelectorAll('.mobile-nav-panel a').forEach(function(a) { a.addEventListener('click', closeMenu); });
    })();
    </script>
    <script>
    (function() {
        function initDropdown(btnId, ddId) {
            var btn = document.getElementById(btnId);
            var dd  = document.getElementById(ddId);
            if (!btn || !dd) return;
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var isHidden = dd.classList.contains('hidden');
                dd.classList.toggle('hidden', !isHidden);
                btn.setAttribute('aria-expanded', String(isHidden));
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !dd.contains(e.target)) {
                    dd.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }
        initDropdown('dashboard-dropdown-btn', 'dashboard-dropdown');
        initDropdown('more-dropdown-btn', 'more-dropdown');
    })();
    </script>
    @auth
    <script>
    (function() {
        var bellBtn  = document.getElementById('bell-btn');
        var bellPop  = document.getElementById('bell-popup');
        var bellList = document.getElementById('bell-list');
        var bellBadge= document.getElementById('bell-badge');
        var csrf     = document.querySelector('meta[name=csrf-token]')?.content || '';
        var icons = {
            program:  '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
            event:    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            post:     '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
            research: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
        };
        var defaultIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>';

        function timeAgo(dateStr) {
            var diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
            if (diff < 60)   return diff + 's ago';
            if (diff < 3600) return Math.floor(diff/60) + 'm ago';
            if (diff < 86400)return Math.floor(diff/3600) + 'h ago';
            return Math.floor(diff/86400) + 'd ago';
        }

        function loadNotifications() {
            fetch('/notifications/recent', {headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
                .then(r => r.json())
                .then(data => {
                    // Badge
                    var mobileBadge = document.getElementById('mobile-bell-badge');
                    if (data.unread > 0) {
                        var label = data.unread > 99 ? '99+' : data.unread;
                        bellBadge.textContent = label;
                        bellBadge.classList.remove('hidden');
                        if (mobileBadge) { mobileBadge.textContent = label; mobileBadge.classList.remove('hidden'); }
                    } else {
                        bellBadge.classList.add('hidden');
                        if (mobileBadge) mobileBadge.classList.add('hidden');
                    }
                    // List
                    if (!data.notifications.length) {
                        bellList.innerHTML = '<div class="px-4 py-6 text-center text-sm" style="color:var(--text-secondary)">No notifications yet.</div>';
                        return;
                    }
                    bellList.innerHTML = data.notifications.map(function(n) {
                        var icon = icons[n.type] || defaultIcon;
                        var unread = !n.read_at;
                        return '<div class="flex gap-3 px-4 py-3 hover:bg-[var(--bg-warm)] transition cursor-pointer notification-row" data-id="'+n.id+'" data-url="'+(n.url||'')+'" style="'+(unread?'background:var(--bg-warm);':'')+'">'
                            + '<span class="shrink-0 mt-0.5 w-7 h-7 rounded-md flex items-center justify-center" style="background:var(--bg-warm);color:var(--accent-orange);">'+icon+'</span>'
                            + '<div class="flex-1 min-w-0">'
                            + '<p class="text-sm font-'+(unread?'semibold':'normal')+' leading-snug truncate" style="color:var(--text-primary)">'+n.title+'</p>'
                            + (n.body ? '<p class="text-xs mt-0.5 line-clamp-1" style="color:var(--text-secondary)">'+n.body+'</p>' : '')
                            + '<p class="text-xs mt-1" style="color:var(--text-secondary)">'+timeAgo(n.created_at)+'</p>'
                            + '</div>'
                            + (unread ? '<span class="w-2 h-2 rounded-full shrink-0 mt-2" style="background:var(--accent-orange);"></span>' : '')
                            + '</div>';
                    }).join('');
                    // Click rows
                    bellList.querySelectorAll('.notification-row').forEach(function(row) {
                        row.addEventListener('click', function() {
                            var id  = row.dataset.id;
                            var url = row.dataset.url;
                            fetch('/notifications/'+id+'/read', {method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}});
                            if (url) window.location.href = url;
                        });
                    });
                });
        }

        if (bellBtn) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var open = !bellPop.classList.contains('hidden');
                bellPop.classList.toggle('hidden', open);
                if (!open) loadNotifications();
            });
            document.addEventListener('click', function(e) {
                if (!document.getElementById('bell-wrap')?.contains(e.target)) {
                    bellPop.classList.add('hidden');
                }
            });
        }

        document.getElementById('bell-mark-all')?.addEventListener('click', function() {
            fetch('/notifications/read-all', {method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}})
                .then(() => { bellBadge.classList.add('hidden'); loadNotifications(); });
        });

        // Load badge count on page load
        loadNotifications();
    })();
    </script>
    @endauth
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('form[method="GET"]');
        forms.forEach(function(form) {
            var sortSelect = form.querySelector('select[name="sort"]');
            var searchInput = form.querySelector('input[name="q"]');
            
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    form.submit();
                });
            }
            
            if (searchInput) {
                var timeout = null;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        form.submit();
                    }, 500);
                });
                
                if (searchInput.value) {
                    var val = searchInput.value;
                    searchInput.focus();
                    searchInput.value = '';
                    searchInput.value = val;
                }
            }
        });
    });
    </script>
    @stack('scripts')
    @include('partials.share-script')
    @if(request()->routeIs('home'))
    <script>
    (function() {
        var header = document.getElementById('site-header');
        var hero = document.getElementById('home-hero');
        if (!header || !header.classList.contains('header-over-hero')) return;
        function updateHeader() {
            var threshold = hero ? Math.max(80, (hero.offsetHeight || window.innerHeight) * 0.15) : 80;
            if (window.scrollY > threshold) {
                header.classList.add('header-solid');
            } else {
                header.classList.remove('header-solid');
            }
        }
        updateHeader();
        window.addEventListener('scroll', updateHeader, { passive: true });
        window.addEventListener('resize', updateHeader);
    })();
    </script>
    @endif
</body>
</html>
