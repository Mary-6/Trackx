<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex">

        <title>{{ setting('company_name', config('app.name')) }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-navy-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-navy-950 px-4 py-10">
            <div>
                <a href="{{ route('home') }}"><x-logo /></a>
            </div>

            <div class="mt-8 w-full overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-xl sm:max-w-md">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-navy-400">Authorised personnel only. All access is logged.</p>
        </div>
    </body>
</html>
