@extends('layouts.public')

@section('title', 'About Us — ' . setting('company_name'))

@section('content')
<section class="bg-navy-950 py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">About Us</p>
        <h1 class="mt-3 font-display text-4xl font-semibold text-white">Built on trust, measured in deliveries.</h1>
        <p class="mt-6 text-lg leading-8 text-navy-200">{{ setting('company_description') }}</p>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-4xl space-y-10 px-4 sm:px-6">
        <div>
            <h2 class="font-display text-2xl font-semibold text-navy-950">Our story — since 2019</h2>
            <p class="mt-3 leading-7 text-navy-600">
                {{ setting('company_name') }} began in 2019 as a small courier operation handling documents and parcels for local businesses. Over the years we grew into a full logistics company moving documents, parcels, electronics, fashion and commercial cargo — domestic and international — with the same obsession we started with: shipments you can actually see.
            </p>
            <p class="mt-4 leading-7 text-navy-600">
                Every consignment on this platform gets a unique tracking number and a verifiable history of confirmed events. This website is the latest chapter of that journey — the same hands-on service, now with live tracking your customers can check from anywhere.
            </p>
            <img src="{{ asset('images/warehouse.jpg') }}" alt="Our fulfilment warehouse" class="mt-6 w-full rounded-2xl object-cover shadow-lg" loading="lazy">
        </div>
        <div>
            <h2 class="font-display text-2xl font-semibold text-navy-950">How we work</h2>
            <p class="mt-3 leading-7 text-navy-600">
                Every status you see on this site is a real record written by our operations team or an authorised tracking integration. We do not simulate locations or fabricate progress. If a shipment's position is unknown, the map says so honestly — transparency is the product.
            </p>
        </div>
        <div class="grid gap-6 sm:grid-cols-3">
            @foreach([
                ['Integrity', 'Confirmed data only. No fake GPS paths, no inflated claims.'],
                ['Security', 'Tamper-evident packaging options and privacy-first tracking pages.'],
                ['Reach', 'Domestic and international routing with customs documentation support.'],
            ] as [$t, $d])
                <div class="rounded-2xl border border-navy-100 bg-navy-50 p-6">
                    <h3 class="font-semibold text-navy-950">{{ $t }}</h3>
                    <p class="mt-2 text-sm leading-6 text-navy-600">{{ $d }}</p>
                </div>
            @endforeach
        </div>
        <div class="rounded-2xl bg-navy-950 p-8 text-center">
            <h2 class="font-display text-2xl font-semibold text-white">Ready to ship with us?</h2>
            <p class="mt-2 text-navy-300">Talk to our team about your consignment — from a single parcel to regular business freight.</p>
            <a href="{{ route('contact') }}" class="mt-6 inline-block rounded-lg bg-gold-400 px-6 py-3 font-semibold text-navy-950 hover:bg-gold-300">Contact Us</a>
        </div>
    </div>
</section>
@endsection
