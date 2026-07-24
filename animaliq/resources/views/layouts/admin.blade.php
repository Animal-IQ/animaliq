<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') – {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    @php $adminLogo = \App\Models\SiteSetting::getByKey('site_logo'); @endphp
    @if($adminLogo)
        <link rel="icon" href="{{ asset('storage/' . $adminLogo) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
    @include('partials.animations')
    <style>
        .admin-sidebar { background: var(--bg-secondary); border-right: 1px solid var(--border-color); }
        .admin-sidebar a, .admin-sidebar button { color: var(--text-secondary); text-decoration: none; background: none; border: none; cursor: pointer; font: inherit; }
        .admin-sidebar a:hover, .admin-sidebar a.active, .admin-sidebar button:hover { background: var(--bg-warm); color: var(--accent-orange); }
        .admin-sidebar .group-label { color: var(--text-secondary); }
        .admin-sidebar-wrap { position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; width: 16rem; max-width: 85vw; flex-direction: column; transform: translateX(-100%); transition: transform 0.25s ease; }
        .admin-sidebar-wrap.open { transform: translateX(0); }
        @media (min-width: 768px) {
            .admin-sidebar-wrap { position: relative; transform: none; width: 16rem; flex-shrink: 0; }
            .admin-sidebar-wrap.open { transform: none; }
        }
        body.admin-sidebar-open { overflow: hidden; }
    </style>
</head>
<body class="theme-bg-primary theme-text-primary min-h-screen antialiased flex">
    <div class="admin-sidebar-mobile-overlay md:hidden" id="admin-sidebar-overlay" aria-hidden="true"></div>
    <aside class="admin-sidebar-wrap admin-sidebar flex min-h-screen" id="admin-sidebar">
        <div class="p-4 theme-header-border">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold theme-accent text-lg flex items-center gap-2">
                @php $adminSideLogo = \App\Models\SiteSetting::getByKey('site_logo'); @endphp
                @if($adminSideLogo)
                    <img src="{{ asset('storage/' . $adminSideLogo) }}" alt="Logo" class="h-6 w-6 object-cover rounded-full inline-block">
                @endif
                Admin
            </a>
        </div>
        <nav class="p-2 flex-1 overflow-y-auto">
            @php
                $adminSections = $adminAllowedSections ?? [];
            @endphp
            <ul class="space-y-0.5">
                @if(in_array('dashboard', $adminSections))
                <li><a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.dashboard')) active @endif">Dashboard</a></li>
                @endif
                <li class="pt-2 mt-2 border-t theme-border"><span class="px-3 text-xs font-semibold group-label uppercase">Content</span></li>
                @if(in_array('departments', $adminSections))
                <li><a href="{{ route('admin.departments.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.departments.*')) active @endif">Departments</a></li>
                @endif
                @if(in_array('programs', $adminSections))
                <li><a href="{{ route('admin.programs.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.programs.*')) active @endif">Programs</a></li>
                @endif
                @if(in_array('events', $adminSections))
                <li><a href="{{ route('admin.events.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.events.*')) active @endif">Events</a></li>
                @endif
                @if(in_array('research', $adminSections))
                <li><a href="{{ route('admin.research.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.research.*')) active @endif">Research</a></li>
                @endif
                @if(in_array('posts', $adminSections))
                <li><a href="{{ route('admin.posts.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.posts.*')) active @endif">Posts</a></li>
                @endif
                @if(in_array('awareness_days', $adminSections))
                <li><a href="{{ route('admin.awareness-days.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.awareness-days.*')) active @endif">Awareness Days</a></li>
                @endif
                @if(in_array('podcasts', $adminSections))
                <li><a href="{{ route('admin.podcasts.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.podcasts.*')) active @endif">Podcasts</a></li>
                @endif
                @if(in_array('forum', $adminSections))
                <li><a href="{{ route('admin.forum.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.forum.*')) active @endif">Forum Posts</a></li>
                @endif
                @if(in_array('quizzes', $adminSections))
                <li><a href="{{ route('admin.quizzes.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.quizzes.*')) active @endif">Quizzes</a></li>
                @endif
                <li class="pt-2 mt-2 border-t theme-border"><span class="px-3 text-xs font-semibold group-label uppercase">Settings &amp; Site</span></li>
                @if(in_array('settings', $adminSections))
                <li><a href="{{ route('admin.settings.sections', 'mission') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.sections') && request()->route('section') === 'mission') active @endif">Mission &amp; Vision</a></li>
                <li><a href="{{ route('admin.settings.sections', 'about') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.sections') && request()->route('section') === 'about') active @endif">About</a></li>
                <li><a href="{{ route('admin.settings.sections', 'socials') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.sections') && request()->route('section') === 'socials') active @endif">Social Media</a></li>
                <li><a href="{{ route('admin.settings.sections', 'contacts') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.sections') && request()->route('section') === 'contacts') active @endif">Contact Details</a></li>
                <li><a href="{{ route('admin.settings.sections', 'donations') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.sections') && request()->route('section') === 'donations') active @endif">Donations &amp; Payments</a></li>
                <li><a href="{{ route('admin.settings.slides') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.settings.slides*')) active @endif">Homepage Slides</a></li>
                @endif
                @if(in_array('team', $adminSections))
                <li><a href="{{ route('admin.team.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.team.*')) active @endif">Team</a></li>
                @endif
                <li class="pt-2 mt-2 border-t theme-border"><span class="px-3 text-xs font-semibold group-label uppercase">Finance &amp; Store</span></li>
                @if(in_array('donations', $adminSections))
                <li><a href="{{ route('admin.donations.campaigns') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.donations.*')) active @endif">Donation Campaigns</a></li>
                @endif
                @if(in_array('products', $adminSections))
                <li><a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.products.*')) active @endif">Products</a></li>
                @endif
                <li class="pt-2 mt-2 border-t theme-border"><span class="px-3 text-xs font-semibold group-label uppercase">System</span></li>
                @if(in_array('users', $adminSections))
                <li><a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.users.*')) active @endif">Users</a></li>
                @endif
                @if(in_array('audit', $adminSections))
                <li><a href="{{ route('admin.audit.index') }}" class="block px-3 py-2 rounded @if(request()->routeIs('admin.audit.*')) active @endif">Audit Log</a></li>
                @endif
            </ul>
        </nav>
        <div class="p-2 border-t theme-border">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded" target="_blank">View Site</a>
            <form method="POST" action="{{ url('/logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm">Logout</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <header class="theme-bg-secondary theme-border border-b px-4 md:px-6 py-3 shrink-0 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <button type="button" class="md:hidden hamburger-btn flex flex-col justify-center gap-1.5 w-10 h-10 p-2 rounded border theme-border shrink-0" id="admin-menu-btn" aria-label="Open admin menu" aria-expanded="false" aria-controls="admin-sidebar">
                    <span></span><span></span><span></span>
                </button>
                <h2 class="text-base md:text-lg font-semibold theme-text-primary truncate">@yield('heading', 'Admin')</h2>
            </div>
            @include('partials.theme-toggle')
        </header>
        <main class="flex-1 p-4 md:p-6 overflow-auto theme-bg-primary main-enter">
            @if (session('success'))
                <div class="mb-4 p-4 rounded theme-alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 rounded theme-alert-error">{{ session('error') }}</div>
            @endif
        @yield('content')
    </main>
    </div>
    @include('partials.admin-crud-modal')
    <script>
    (function() {
        var btn = document.getElementById('admin-menu-btn');
        var sidebar = document.getElementById('admin-sidebar');
        var overlay = document.getElementById('admin-sidebar-overlay');
        function openSidebar() {
            if (sidebar) sidebar.classList.add('open');
            if (overlay) { overlay.classList.add('open'); overlay.setAttribute('aria-hidden', 'false'); }
            if (btn) { btn.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); btn.setAttribute('aria-label', 'Close admin menu'); }
            document.body.classList.add('admin-sidebar-open');
        }
        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) { overlay.classList.remove('open'); overlay.setAttribute('aria-hidden', 'true'); }
            if (btn) { btn.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); btn.setAttribute('aria-label', 'Open admin menu'); }
            document.body.classList.remove('admin-sidebar-open');
        }
        if (btn) btn.addEventListener('click', function() { (sidebar && sidebar.classList.contains('open')) ? closeSidebar() : openSidebar(); });
        if (overlay) overlay.addEventListener('click', closeSidebar);
        if (sidebar) { sidebar.querySelectorAll('a').forEach(function(a) { a.addEventListener('click', closeSidebar); }); }
    })();
    </script>
</body>
</html>
