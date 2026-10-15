@extends('layouts.public')

@section('title', 'Track Shipment — ' . setting('company_name'))
@section('meta_description', 'Track your shipment using your unique tracking number. View confirmed status, history and last recorded location.')

@section('content')
<section class="bg-navy-950 py-20">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        <h1 class="font-display text-4xl font-semibold text-white">Track Your Shipment</h1>
        <p class="mt-4 text-navy-300">Enter the tracking number you received at booking to view the latest confirmed status and history.</p>

        <form action="{{ route('track.lookup') }}" method="POST" class="mt-10 flex flex-col gap-3 sm:flex-row">
            @csrf
            <label for="tracking_number" class="sr-only">Tracking number</label>
            <input id="tracking_number" name="tracking_number" type="text" required autofocus
                   value="{{ old('tracking_number', $trackingNumber ?? '') }}"
                   placeholder="ACE-XXXXXX"
                   class="w-full rounded-lg border-0 bg-white/10 px-5 py-4 text-center text-lg tracking-wider text-white placeholder-navy-400 ring-1 ring-inset ring-white/20 focus:ring-2 focus:ring-gold-400 sm:text-left">
            <button type="submit" class="shrink-0 rounded-lg bg-gold-400 px-8 py-4 font-semibold text-navy-950 transition hover:bg-gold-300">Track</button>
        </form>
        @error('tracking_number')<p class="mt-3 text-sm text-red-300">{{ $message }}</p>@enderror

        <p class="mt-8 text-xs text-navy-400">Tracking numbers are unique and unguessable. Lookups are rate-limited to protect shipment privacy.</p>
    </div>
</section>
@endsection
