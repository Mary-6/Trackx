<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('company_name', config('app.name')) . ' — ' . setting('company_tagline', 'Shipment Tracking'))</title>
    <meta name="description" content="@yield('meta_description', setting('company_description', 'Secure, transparent shipment tracking for parcels, freight and goods worldwide.'))">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="@yield('title', setting('company_name', config('app.name')))">
    <meta property="og:description" content="@yield('meta_description', setting('company_description', ''))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('robots')<meta name="robots" content="@yield('robots')">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="flex min-h-full flex-col bg-white font-sans text-navy-900 antialiased">

    {{-- Navigation --}}
    <header class="sticky top-0 z-40 border-b border-navy-800/50 bg-navy-950/95 backdrop-blur" x-data="{ open: false }">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="flex items-center"><x-logo /></a>

            <div class="hidden items-center gap-1 lg:flex">
                <a href="{{ route('home') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('home') ? 'text-gold-400' : '' }}">Home</a>
                <a href="{{ route('track') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('track*') ? 'text-gold-400' : '' }}">Track Shipment</a>
                <a href="{{ route('services') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('services') ? 'text-gold-400' : '' }}">Services</a>
                <a href="{{ route('about') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('about') ? 'text-gold-400' : '' }}">About Us</a>
                <a href="{{ route('contact') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('contact') ? 'text-gold-400' : '' }}">Contact</a>
                <a href="{{ route('faq') }}" class="rounded-md px-3 py-2 text-sm font-medium text-navy-100 hover:bg-navy-800 hover:text-white {{ request()->routeIs('faq') ? 'text-gold-400' : '' }}">Support</a>
                <a href="{{ route('track') }}" class="ml-3 rounded-lg bg-gold-400 px-4 py-2 text-sm font-semibold text-navy-950 shadow hover:bg-gold-300 transition">Track Now</a>
            </div>

            <button type="button" class="rounded-md p-2 text-navy-100 hover:bg-navy-800 lg:hidden" @click="open = !open" aria-label="Toggle menu" :aria-expanded="open">
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </nav>

        <div x-show="open" x-cloak x-transition class="border-t border-navy-800 lg:hidden" @click.away="open = false">
            <div class="space-y-1 px-4 py-3">
                <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">Home</a>
                <a href="{{ route('track') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">Track Shipment</a>
                <a href="{{ route('services') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">Services</a>
                <a href="{{ route('about') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">About Us</a>
                <a href="{{ route('contact') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">Contact</a>
                <a href="{{ route('faq') }}" class="block rounded-md px-3 py-2 text-base font-medium text-navy-100 hover:bg-navy-800">Customer Support</a>
                <a href="{{ route('track') }}" class="block rounded-md bg-gold-400 px-3 py-2 text-base font-semibold text-navy-950">Track Now</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-navy-950 text-navy-200">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-4">
                <div class="md:col-span-2">
                    <x-logo />
                    <p class="mt-4 max-w-md text-sm leading-6 text-navy-300">{{ setting('footer_about', setting('company_description')) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gold-400">Company</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white">Services</a></li>
                        <li><a href="{{ route('track') }}" class="hover:text-white">Track Shipment</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white">FAQ &amp; Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gold-400">Contact</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        @if(setting('contact_email'))<li>{{ setting('contact_email') }}</li>@endif
                        @if(setting('support_phone'))<li>{{ setting('support_phone') }}</li>@endif
                        @if(setting('office_address'))<li>{{ setting('office_address') }}</li>@endif
                        @if(setting('business_hours'))<li class="text-navy-400">{{ setting('business_hours') }}</li>@endif
                    </ul>
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-navy-800 pt-6 text-xs text-navy-400 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ setting('company_name', config('app.name')) }}. All rights reserved.</p>
                <div class="flex gap-5">
                    <a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="hover:text-white">Terms &amp; Conditions</a>
                    <a href="{{ route('shipping-policy') }}" class="hover:text-white">Shipping Policy</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Smartsupp live chat — official widget, loaded once when a key is configured --}}
    @if($smartsuppKey = config('services.smartsupp.key'))
        <!-- Smartsupp Live Chat script -->
        <script type="text/javascript">
        var _smartsupp = _smartsupp || {};
        _smartsupp.key = '{{ $smartsuppKey }}';
        window.smartsupp||(function(d) {
            var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
            s=d.getElementsByTagName('script')[0];c=d.createElement('script');
            c.type='text/javascript';c.charset='utf-8';c.async=true;
            c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
        })(document);
        </script>
    @endif
    @stack('scripts')
</body>
</html>
