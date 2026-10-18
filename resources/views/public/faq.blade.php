@extends('layouts.public')

@section('title', 'FAQ — ' . setting('company_name'))
@section('meta_description', 'Answers about shipment tracking, statuses, privacy, insurance and delivery.')

@section('content')
<section class="bg-navy-950 py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gold-400">Customer Support</p>
        <h1 class="mt-3 font-display text-4xl font-semibold text-white">Frequently Asked Questions</h1>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6" x-data="{ open: 0 }">
        @php
            $faqs = [
                ['How do I track my shipment?', 'Enter your tracking number on the Track Shipment page. Numbers are 12 mixed letters and digits, like T8K3L2M9Q1RX, and are provided at booking. You will see the latest confirmed status, the full event timeline, and a map of the last confirmed location where coordinates exist.'],
                ['What do the tracking statuses mean?', 'Statuses progress from "Shipment Created" through "Dispatched", "In Transit", customs milestones where relevant, "Out for Delivery" and "Delivered". Exception statuses like "On Hold" or "Delivery Exception" mean our team is actively handling an issue — contact support for details.'],
                ['Is the map live GPS?', 'The map shows the latest confirmed, recorded location — written by our operations team or an authorised courier/GPS integration — plus origin and destination markers. It is honest data: if no coordinates have been recorded yet, the map says so rather than guessing.'],
                ['Why can\'t I see the recipient\'s address or phone number?', 'Privacy by design. Public tracking pages only display non-sensitive shipment information. Contact details and full street addresses are restricted to authorised staff.'],
                ['Do you ship high-value or fragile goods?', 'Yes — our secure-goods channel uses tamper-evident, unbranded packaging, optional declared-value insurance and signature confirmation on delivery.'],
                ['How are delivery estimates calculated?', 'Estimated delivery dates are set by our team when the shipment is booked or dispatched, based on the route and shipping method. They are estimates, not guarantees; your tracking page always shows the latest confirmed information.'],
                ['What happens if my shipment is delayed?', 'If a shipment passes its estimated delivery date it is flagged as delayed on our operations dashboard and reviewed. Where notifications are configured, recipients are emailed about significant delays and exceptions.'],
                ['Do I need an account?', 'No. Tracking works with just your tracking number. Accounts exist only for our staff and integration partners.'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach($faqs as $i => [$q, $a])
                <div class="rounded-xl border border-navy-100">
                    <button type="button" class="flex w-full items-center justify-between px-6 py-4 text-left font-medium text-navy-900" @click="open = open === {{ $i }} ? null : {{ $i }}">
                        {{ $q }}
                        <svg class="h-5 w-5 text-navy-400 transition" :class="open === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition.opacity.duration.200ms class="px-6 pb-5 text-sm leading-6 text-navy-600">{{ $a }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 rounded-2xl bg-navy-50 p-8 text-center">
            <p class="text-navy-700">Still stuck? <a href="{{ route('contact') }}" class="font-semibold text-navy-900 underline decoration-gold-400 underline-offset-4">Message our support team</a> — we reply to every enquiry.</p>
        </div>
    </div>
</section>
@endsection
