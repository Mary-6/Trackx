<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — {{ setting('company_name', config('app.name')) }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-navy-50 font-sans text-navy-900 antialiased" x-data="{ sidebar: false, expanded: ['shipping'] }">

@php
    // Grouped, collapsible sidebar — same structure as the Aetherian admin.
    $nav = [
        ['type' => 'link', 'label' => 'Control Panel', 'patterns' => 'admin.dashboard', 'route' => 'admin.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10'],
        ['type' => 'group', 'key' => 'shipping', 'label' => 'Shipping', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'patterns' => 'admin.shipments.*,admin.categories.*,admin.enquiries.*', 'children' => [
            ['label' => 'Shipping Control Panel', 'route' => 'admin.shipments.index', 'patterns' => 'admin.shipments.index,admin.shipments.show,admin.shipments.edit'],
            ['label' => 'Create Shipment', 'route' => 'admin.shipments.create', 'patterns' => 'admin.shipments.create'],
        ]],
        ['type' => 'group', 'key' => 'users', 'label' => 'Users', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'patterns' => 'admin.customers.*,admin.users.*', 'children' => [
            ['label' => 'Client List', 'route' => 'admin.customers.index', 'patterns' => 'admin.customers.*'],
            ['label' => 'Users & Roles', 'route' => 'admin.users.index', 'patterns' => 'admin.users.*', 'super' => true],
        ]],
        ['type' => 'link', 'label' => 'Settings', 'patterns' => 'admin.settings.*', 'route' => 'admin.settings.edit', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-.996.608 2.296.07 2.572-1.065z', 'super' => true],
        ['type' => 'link', 'label' => 'Audit Log', 'patterns' => 'admin.audit.*', 'route' => 'admin.audit.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'super' => true],
        ['type' => 'link', 'label' => 'Account Profile', 'patterns' => 'profile.*', 'route' => 'profile.edit', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
    $isActive = fn (string $patterns) => collect(explode(',', $patterns))->contains(fn ($p) => request()->routeIs(trim($p)));
@endphp

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 z-40 flex w-64 transform flex-col bg-navy-950 transition-transform duration-300 lg:static lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center border-b border-navy-800 px-6">
            <a href="{{ route('admin.dashboard') }}"><x-logo /></a>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-4" aria-label="Admin navigation">
            @foreach($nav as $item)
                @if(($item['super'] ?? false) && ! auth()->user()->isSuperAdmin())
                    @continue
                @endif

                @if($item['type'] === 'link')
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive($item['patterns']) ? 'bg-navy-800 text-gold-400' : 'text-navy-300 hover:bg-navy-800 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                        <span class="flex-1">{{ $item['label'] }}</span>
                    </a>
                @else
                    <div>
                        <button type="button" @click="expanded.includes('{{ $item['key'] }}') ? expanded = expanded.filter(k => k !== '{{ $item['key'] }}') : expanded.push('{{ $item['key'] }}')"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive($item['patterns']) ? 'text-gold-400' : 'text-navy-300 hover:bg-navy-800 hover:text-white' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="expanded.includes('{{ $item['key'] }}') && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="expanded.includes('{{ $item['key'] }}')" x-transition.opacity.duration.150ms class="ml-6 mt-1 space-y-1">
                            @foreach($item['children'] as $child)
                                @if(($child['super'] ?? false) && ! auth()->user()->isSuperAdmin())
                                    @continue
                                @endif
                                <a href="{{ route($child['route']) }}"
                                   class="block rounded-lg px-3 py-2 text-sm transition-colors {{ $isActive($child['patterns']) ? 'bg-navy-800 text-gold-400' : 'text-navy-400 hover:bg-navy-800 hover:text-white' }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-navy-800 p-4">
            <p class="truncate px-3 text-sm font-medium text-white">{{ auth()->user()->name }}</p>
            <p class="px-3 text-xs text-navy-400">{{ auth()->user()->roleLabel() }}</p>
            <div class="mt-3 space-y-1">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-navy-300 transition-colors hover:bg-navy-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Go to Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-navy-300 transition-colors hover:bg-navy-800 hover:text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <div x-show="sidebar" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-30 bg-black/50 lg:hidden" @click="sidebar = false"></div>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-navy-100 bg-white px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-md p-2 text-navy-600 hover:bg-navy-50 lg:hidden" @click="sidebar = !sidebar" aria-label="Toggle sidebar">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-navy-950">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\Shipment::class)
                    <a href="{{ route('admin.shipments.create') }}" class="rounded-lg bg-gold-400 px-4 py-2 text-sm font-semibold text-navy-950 transition hover:bg-gold-300">+ New Shipment</a>
                @endcan
                <a href="{{ route('home') }}" class="text-sm text-navy-500 transition hover:text-navy-800" target="_blank" rel="noopener">View site &nearr;</a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-200" role="status">{{ session('success') }}</div>
            @endif
            @if(session('plainTextToken'))
                <div class="mb-6 rounded-lg bg-amber-50 p-4 text-sm text-amber-900 ring-1 ring-amber-200" role="alert">
                    <p class="font-semibold">New integration token — copy it now, it will not be shown again:</p>
                    <code class="mt-2 block break-all rounded bg-white p-2 font-mono text-xs ring-1 ring-amber-200">{{ session('plainTextToken') }}</code>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
