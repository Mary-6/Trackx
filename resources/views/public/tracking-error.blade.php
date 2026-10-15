@extends('layouts.public')

@section('title', 'Tracking Result — ' . setting('company_name'))
@section('robots', 'noindex')

@section('content')
<section class="bg-navy-50 py-20">
    <div class="mx-auto max-w-xl px-4 text-center sm:px-6">
        <div class="rounded-2xl border border-navy-100 bg-white p-10 shadow-sm">
            <svg class="mx-auto h-14 w-14 text-navy-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h1 class="mt-5 font-display text-2xl font-semibold text-navy-950">Shipment Not Found</h1>
            <p class="mt-3 text-sm leading-6 text-navy-600">{{ $message ?? 'No shipment matches that tracking number. Check for typos and try again.' }}</p>
            <a href="{{ route('track') }}" class="mt-8 inline-block rounded-lg bg-navy-800 px-6 py-3 font-semibold text-white hover:bg-navy-700">Try another number</a>
            <p class="mt-4 text-xs text-navy-400">Need help? <a href="{{ route('contact') }}" class="underline">Contact support</a>.</p>
        </div>
    </div>
</section>
@endsection
