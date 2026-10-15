@extends('layouts.public')

@section('title', 'Services — ' . setting('company_name'))
@section('meta_description', 'International shipping, parcel delivery, secure goods handling and business logistics.')

@section('content')
<section class="bg-navy-950 py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Our Services</p>
        <h1 class="mt-3 font-display text-4xl font-semibold text-white">One platform. Every shipment type.</h1>
        <p class="mt-6 text-lg leading-8 text-navy-200">Our network carries documents, parcels, electronics, fashion and commercial cargo — every shipment with the same verified tracking.</p>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2">
            @foreach([
                ['Secure & High-Value Goods', 'Tamper-evident, unbranded packaging; optional declared-value insurance; signature confirmation on delivery; confirmed-location updates throughout the journey.', 'images/fragile.jpg'],
                ['International Shipping', 'Cross-border delivery with customs documentation assistance, customs-reference recording and milestone updates at each handover point.', 'images/aerial.jpg'],
                ['Package & Parcel Delivery', 'Everyday parcels, documents and gifts delivered door-to-door with a unique tracking number and full event history.', 'images/parcels.jpg'],
                ['Business Logistics', 'Account-managed collections, consolidated freight and scheduled runs for commercial clients. CSV reporting available for your shipments.', 'images/containers.jpg'],
            ] as [$title, $text, $img])
                <div class="group overflow-hidden rounded-2xl border border-navy-100 transition duration-300 hover:-translate-y-1 hover:border-gold-400 hover:shadow-xl" data-reveal>
                    <div class="h-44 overflow-hidden">
                        <img src="{{ asset($img) }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    </div>
                    <div class="p-8">
                        <h2 class="font-display text-xl font-semibold text-navy-950">{{ $title }}</h2>
                        <p class="mt-3 leading-7 text-navy-600">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="mt-16 font-display text-2xl font-semibold text-navy-950">Shipment categories we handle</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $category)
                <div class="rounded-xl border border-navy-100 bg-navy-50 p-5">
                    <h3 class="font-medium text-navy-900">{{ $category->name }}</h3>
                    @if($category->description)<p class="mt-1 text-xs leading-5 text-navy-500">{{ $category->description }}</p>@endif
                </div>
            @endforeach
        </div>

        <div class="mt-14 rounded-2xl bg-navy-950 p-8 text-center">
            <h2 class="font-display text-2xl font-semibold text-white">Have something to send?</h2>
            <a href="{{ route('contact') }}" class="mt-6 inline-block rounded-lg bg-gold-400 px-6 py-3 font-semibold text-navy-950 hover:bg-gold-300">Get a Quote</a>
        </div>
    </div>
</section>
@endsection
