@extends('layouts.public')

@section('title', setting('company_name') . ' — ' . setting('company_tagline', 'Premium Shipment Tracking'))

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden bg-navy-950">
    <div class="absolute inset-0" aria-hidden="true">
        <img src="{{ asset('images/hero.jpg') }}" alt="" class="h-full w-full object-cover opacity-30" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/85 to-navy-950/40"></div>
        <svg class="absolute inset-0 h-full w-full opacity-30" preserveAspectRatio="none" viewBox="0 0 1200 600" fill="none">
            <path d="M-20 480 C 200 400, 380 520, 620 430 S 1000 300, 1240 380" stroke="#d4a017" stroke-opacity="0.4" stroke-width="2" stroke-dasharray="8 8"/>
            <path d="M-20 200 C 240 260, 460 140, 700 220 S 1040 330, 1240 240" stroke="#748cc0" stroke-opacity="0.3" stroke-width="2" stroke-dasharray="6 10"/>
        </svg>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="max-w-3xl" data-reveal>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Secure Global Logistics</p>
            <h1 class="mt-4 font-display text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                Your Shipment. Tracked Every Step of the Way.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-navy-200">
                Track your packages and deliveries with clear shipment updates and convenient access to tracking information.
            </p>

            <form action="{{ route('track.lookup') }}" method="POST" class="mt-10 flex max-w-xl flex-col gap-3 sm:flex-row">
                @csrf
                <label for="hero-tracking" class="sr-only">Tracking number</label>
                <input id="hero-tracking" name="tracking_number" type="text" required
                       placeholder="Enter tracking number — e.g. T8K3L2M9Q1RX"
                       class="w-full rounded-lg border-0 bg-white/10 px-5 py-4 text-white placeholder-navy-300 ring-1 ring-inset ring-white/20 backdrop-blur transition focus:bg-white/15 focus:ring-2 focus:ring-gold-400">
                <button type="submit" class="shrink-0 rounded-lg bg-gold-400 px-8 py-4 font-semibold text-navy-950 shadow-lg shadow-gold-500/20 transition hover:bg-gold-300">
                    Track Now
                </button>
            </form>
            @error('tracking_number')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror

            <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-navy-300">
                @foreach(['Real database records', 'Confirmed locations only', 'Privacy-first design'] as $chip)
                    <span class="flex items-center gap-2"><svg class="h-4 w-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 111.4-1.4l3.3 3.3 7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg> {{ $chip }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl" data-reveal>
            <h2 class="font-display text-3xl font-semibold text-navy-950">How Shipment Tracking Works</h2>
            <p class="mt-3 text-navy-600">Three simple steps between you and complete visibility of your consignment.</p>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-3">
            @foreach([
                ['1', 'Get your tracking number', 'Every registered shipment receives a unique, secure tracking number at the point of booking.'],
                ['2', 'Enter it on the tracking page', 'Use the tracker above or the Track Shipment page to retrieve the latest confirmed status.'],
                ['3', 'Follow every confirmed update', 'See status changes, confirmed locations on the map, and your full tracking history in order.'],
            ] as [$num, $title, $text])
                <div class="relative rounded-2xl border border-navy-100 bg-navy-50 p-8 transition duration-300 hover:-translate-y-1 hover:shadow-lg" data-reveal>
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-800 font-display text-xl font-bold text-gold-400">{{ $num }}</span>
                    <h3 class="mt-5 text-lg font-semibold text-navy-950">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-navy-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Services --}}
<section class="bg-navy-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal>
            <div>
                <h2 class="font-display text-3xl font-semibold text-navy-950">Our Shipping Services</h2>
                <p class="mt-3 max-w-2xl text-navy-600">From a single parcel to consolidated commercial cargo — every category is handled with the same discipline.</p>
            </div>
            <a href="{{ route('services') }}" class="shrink-0 font-semibold text-navy-800 transition hover:text-gold-600">All services &rarr;</a>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $services = [
                    ['Secure & High-Value Goods', 'Discreet tamper-evident packaging, optional declared-value insurance and signature confirmation on delivery.', 'images/fragile.jpg'],
                    ['International Shipping', 'Customs documentation support and multi-leg routing across our international partner network.', 'images/aerial.jpg'],
                    ['Package & Parcel Delivery', 'Reliable door-to-door delivery for personal parcels, documents and e-commerce orders.', 'images/parcels.jpg'],
                    ['Business Logistics', 'Scheduled collections, consolidated freight and account-managed shipping for commercial clients.', 'images/warehouse.jpg'],
                ];
            @endphp
            @foreach($services as [$title, $text, $img])
                <div class="group overflow-hidden rounded-2xl border border-navy-100 bg-white transition duration-300 hover:-translate-y-1 hover:border-gold-400 hover:shadow-xl" data-reveal>
                    <div class="h-36 overflow-hidden">
                        <img src="{{ asset($img) }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-navy-950">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-navy-600">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Signature service strip --}}
<section class="bg-navy-950 py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div data-reveal>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Handled With Care</p>
            <h2 class="mt-3 font-display text-3xl font-semibold text-white">Secure &amp; Fragile Goods Channel</h2>
            <p class="mt-4 leading-7 text-navy-200">
                Some consignments deserve more than a standard parcel service. Our secure-goods channel pairs discreet, tamper-evident packaging with confirmed milestone tracking, optional insurance and signature-on-delivery confirmation.
            </p>
            <ul class="mt-6 space-y-3 text-sm text-navy-200">
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/15 text-xs text-gold-400">✓</span> Tamper-evident, unbranded outer packaging</li>
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/15 text-xs text-gold-400">✓</span> Optional declared-value insurance</li>
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/15 text-xs text-gold-400">✓</span> Confirmed-location updates and delivery proof</li>
            </ul>
            <a href="{{ route('services') }}" class="mt-8 inline-block rounded-lg border border-gold-400 px-6 py-3 font-semibold text-gold-400 transition hover:bg-gold-400 hover:text-navy-950">Explore services</a>
        </div>
        <div class="relative" data-reveal>
            <img src="{{ asset('images/fragile.jpg') }}" alt="A fragile parcel being handled with care" class="w-full rounded-2xl object-cover shadow-2xl ring-1 ring-white/10" loading="lazy">
            <div class="absolute -bottom-4 -right-4 h-24 w-24 rounded-2xl border-4 border-navy-950 bg-gold-400" aria-hidden="true"></div>
        </div>
    </div>
</section>

{{-- Global reach --}}
<section class="bg-white py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="grid grid-cols-2 gap-4" data-reveal>
            <img src="{{ asset('images/aerial.jpg') }}" alt="Container terminal seen from above" class="rounded-2xl object-cover shadow-lg" loading="lazy">
            <img src="{{ asset('images/air.jpg') }}" alt="Cargo aircraft on the runway" class="mt-8 rounded-2xl object-cover shadow-lg" loading="lazy">
        </div>
        <div data-reveal>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Worldwide Network</p>
            <h2 class="mt-3 font-display text-3xl font-semibold text-navy-950">International Shipping, Door to Door</h2>
            <p class="mt-4 leading-7 text-navy-600">
                Air, sea and road freight coordinated through vetted partners. We handle customs documentation, consolidated cargo and multi-leg routing — while your tracking page reflects every confirmed milestone.
            </p>
            <ul class="mt-6 space-y-3 text-sm text-navy-700">
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/20 text-xs text-gold-600">✓</span> Sea, air and road freight options</li>
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/20 text-xs text-gold-600">✓</span> Customs processing handled and logged as tracking events</li>
                <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gold-400/20 text-xs text-gold-600">✓</span> Business accounts with scheduled collections</li>
            </ul>
        </div>
    </div>
</section>

{{-- Why choose us --}}
<section class="bg-navy-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl font-semibold text-navy-950" data-reveal>Why Choose Us</h2>
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['Verified updates', 'Every tracking event is a real, recorded update — never simulated movement.', 'M9 12l2 2 4-4m5.6 2A9 9 0 1121 12a9 9 0 01-5.6 8.3z'],
                ['Privacy built in', 'Addresses, phone numbers and internal notes never appear on public tracking pages unless the business enables them.', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                ['Any shipment type', 'Documents, electronics, fashion, parcels, commercial cargo — one platform for all of it.', 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8'],
                ['Human support', 'Live chat and a staffed support desk that can see the same confirmed data you can.', 'M18 10a6 6 0 00-12 0v3a2 2 0 002 2h1a2 2 0 002-2v-1a2 2 0 00-2-2H8m4 11v-3a2 2 0 012-2h1'],
            ] as [$title, $text, $icon])
                <div class="rounded-2xl border border-navy-100 bg-white p-7 transition duration-300 hover:-translate-y-1 hover:shadow-lg" data-reveal>
                    <svg class="h-8 w-8 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    <h3 class="mt-4 font-semibold text-navy-950">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-navy-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="bg-white py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-center font-display text-3xl font-semibold text-navy-950" data-reveal>Frequently Asked Questions</h2>
        <div class="mt-10 space-y-4" x-data="{ open: null }" data-reveal>
            @foreach([
                ['How do I track my shipment?', 'Enter your tracking number (a 12-character code like T8K3L2M9Q1RX) on the Track Shipment page. You will see the latest confirmed status, timeline and, where available, the last confirmed location on the map.'],
                ['Do I need an account to track?', 'No. Tracking is public but privacy-limited — only the shipment details the business has approved for display are shown.'],
                ['What does the map show?', 'The map displays the last confirmed location recorded by our staff or an authorised tracking integration, plus origin and destination markers when coordinates exist. It is not a simulated animation.'],
                ['Can you ship high-value or fragile goods?', 'Yes — our secure-goods channel includes discreet packaging, optional insurance and signature confirmation. Contact support before dispatch for requirements.'],
            ] as $i => [$q, $a])
                <div class="rounded-xl border border-navy-100 bg-white shadow-sm">
                    <button type="button" class="flex w-full items-center justify-between px-6 py-4 text-left font-medium text-navy-900" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}">
                        {{ $q }}
                        <svg class="h-5 w-5 text-navy-400 transition" :class="open === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition.opacity.duration.200ms class="px-6 pb-5 text-sm leading-6 text-navy-600">{{ $a }}</div>
                </div>
            @endforeach
        </div>
        <p class="mt-8 text-center text-sm text-navy-600">More questions? <a href="{{ route('faq') }}" class="font-semibold text-navy-800 underline decoration-gold-400 underline-offset-4">Read the full FAQ</a> or <a href="{{ route('contact') }}" class="font-semibold text-navy-800 underline decoration-gold-400 underline-offset-4">contact support</a>.</p>
    </div>
</section>

{{-- Testimonials --}}
<section class="bg-white pb-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl" data-reveal>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-500">Customer Reviews</p>
            <h2 class="mt-3 font-display text-3xl font-semibold text-navy-950 sm:text-4xl">Trusted by senders and receivers</h2>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $reviews = [
                    ['quote' => 'My parcel arrived two days early and every status update was accurate — I could see exactly where it was the whole way.', 'name' => 'Amara N.', 'role' => 'Boutique owner', 'rating' => 5],
                    ['quote' => 'They handled a fragile consignment professionally and the live map was genuinely reassuring throughout.', 'name' => 'Daniel K.', 'role' => 'Electronics reseller', 'rating' => 4.5],
                    ['quote' => 'Good communication from booking to delivery. There was a short customs delay but they kept me informed.', 'name' => 'Grace A.', 'role' => 'Online shopper', 'rating' => 4],
                    ['quote' => 'Fast pickup, transparent pricing, and the tracking page with the barcode is genuinely useful.', 'name' => 'Marcus T.', 'role' => 'Small business owner', 'rating' => 5],
                ];
            @endphp
            @foreach($reviews as $review)
                <div class="flex flex-col rounded-2xl border border-navy-100 bg-navy-50 p-6" data-reveal>
                    <x-star-rating :rating="$review['rating']" />
                    <p class="mt-4 flex-1 text-sm leading-6 text-navy-600">"{{ $review['quote'] }}"</p>
                    <div class="mt-5 flex items-center gap-3 border-t border-navy-100 pt-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-navy-950 text-xs font-bold text-gold-400">{{ strtoupper(collect(explode(' ', $review['name']))->map(fn($w) => $w[0])->take(2)->implode('')) }}</span>
                        <div>
                            <p class="text-sm font-semibold text-navy-950">{{ $review['name'] }}</p>
                            <p class="text-xs text-navy-400">{{ $review['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Support CTA --}}
<section class="bg-white pb-20 pt-4">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-navy-950 px-8 py-14 text-center sm:px-14" data-reveal>
            <h2 class="font-display text-3xl font-semibold text-white">Need a hand with your shipment?</h2>
            <p class="mx-auto mt-3 max-w-xl text-navy-300">Our support team is available {{ setting('business_hours', 'during business hours') }}. Use live chat or send us a message — we reply to every enquiry.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="rounded-lg bg-gold-400 px-6 py-3 font-semibold text-navy-950 transition hover:bg-gold-300">Contact Support</a>
                <a href="{{ route('track') }}" class="rounded-lg border border-navy-600 px-6 py-3 font-semibold text-white transition hover:bg-navy-800">Track a Shipment</a>
            </div>
        </div>
    </div>
</section>

@endsection
