@extends('layouts.public')

@section('title', 'Contact Us — ' . setting('company_name'))

@section('content')
<section class="bg-navy-950 py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Contact Us</p>
        <h1 class="mt-3 font-display text-4xl font-semibold text-white">We reply to every enquiry.</h1>
        <p class="mt-6 text-lg text-navy-200">Questions about a shipment, a quote, or a delivery? Reach us any of these ways.</p>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="space-y-6">
            <div class="rounded-2xl border border-navy-100 p-6">
                <h3 class="font-semibold text-navy-950">Email</h3>
                <p class="mt-1 text-sm text-navy-600">{{ setting('contact_email') }}</p>
            </div>
            <div class="rounded-2xl border border-navy-100 p-6">
                <h3 class="font-semibold text-navy-950">Phone</h3>
                <p class="mt-1 text-sm text-navy-600">{{ setting('support_phone') }}</p>
            </div>
            <div class="rounded-2xl border border-navy-100 p-6">
                <h3 class="font-semibold text-navy-950">Office</h3>
                <p class="mt-1 text-sm text-navy-600">{{ setting('office_address') }}</p>
                <p class="mt-1 text-xs text-navy-400">{{ setting('business_hours') }}</p>
            </div>
            <div class="rounded-2xl border border-gold-400/40 bg-gold-400/10 p-6">
                <h3 class="font-semibold text-navy-950">Live chat</h3>
                <p class="mt-1 text-sm text-navy-600">When configured, our live chat appears at the bottom-right of every page for instant support.</p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-navy-100 p-8 shadow-sm">
                <h2 class="font-display text-2xl font-semibold text-navy-950">Send a message</h2>

                @if(session('success'))
                    <div class="mt-4 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-200">{{ session('success') }}</div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="mt-6 grid gap-5 sm:grid-cols-2">
                    @csrf
                    <div>
                        <label for="name" class="text-sm font-medium text-navy-800">Name *</label>
                        <input id="name" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-medium text-navy-800">Email *</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="tracking_number" class="text-sm font-medium text-navy-800">Tracking number (optional)</label>
                        <input id="tracking_number" name="tracking_number" value="{{ old('tracking_number', request('tracking')) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label for="subject" class="text-sm font-medium text-navy-800">Subject</label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="text-sm font-medium text-navy-800">Message *</label>
                        <textarea id="message" name="message" rows="5" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="rounded-lg bg-navy-800 px-8 py-3 font-semibold text-white transition hover:bg-navy-700">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
