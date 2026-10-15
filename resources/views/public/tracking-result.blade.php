@extends('layouts.public')

@section('title', 'Tracking ' . $shipment->tracking_number . ' — ' . setting('company_name'))
@section('robots', 'noindex, nofollow')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
@php
    $partyLabels = [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone Number',
        'house_number' => 'House Number',
        'street' => 'Street Address',
        'city_state' => 'City/State',
        'country' => 'Country',
    ];

    $infoRows = array_filter([
        'Title' => $payload['title'] ?: $payload['product_name'],
        'Method of Shipment' => $payload['shipping_method'],
        'Status' => $payload['status']['name'] ?? null,
        'Category' => $payload['category'],
        'Product Name' => $payload['product_name'],
        'Weight' => $payload['weight_kg'] !== null ? $payload['weight_kg'].' kg' : null,
        'Total Shipment' => $payload['quantity'],
        'Description' => $payload['description'],
        'Departure Location' => collect([$payload['origin']['city'], $payload['origin']['country']])->filter()->implode(', ') ?: null,
        'Departure Date' => $shipment->dispatch_date?->toFormattedDateString(),
        'Departure Time' => $payload['departure_time'],
        'Pickup/Delivery Location' => collect([$payload['destination']['city'], $payload['destination']['country']])->filter()->implode(', ') ?: null,
        'Pickup/Delivery Date' => $shipment->estimated_delivery_date?->toFormattedDateString(),
        'Pickup/Delivery Time' => $payload['delivery_time'],
        'Current Location' => $payload['current_location'],
        'Delivered' => $shipment->delivered_at?->toDayDateTimeString(),
    ], fn ($v) => $v !== null && $v !== '');
@endphp

{{-- A. Package tracking information --}}
<section class="border-b border-navy-100 bg-white py-12">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
        <h1 class="font-display text-3xl font-bold uppercase tracking-wide text-navy-900 sm:text-4xl">Package Tracking Information</h1>
        <div class="mx-auto mt-3 h-0.5 w-20 bg-gold-500"></div>

        <div class="mt-8 flex justify-center overflow-x-auto" aria-hidden="true">{!! $barcode !!}</div>
        <p class="mt-4 font-mono text-2xl font-bold tracking-[0.15em] text-navy-800 sm:text-3xl">{{ $shipment->tracking_number }}</p>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <span class="text-sm font-semibold uppercase tracking-wider text-navy-500">Status:</span>
            <x-status-badge :status="$shipment->status" />
        </div>
        @if($payload['status_reason'])
            <p class="mt-3 text-sm text-navy-600"><span class="font-semibold text-navy-800">Reason for consignment status:</span> {{ $payload['status_reason'] }}</p>
        @endif
    </div>
</section>

<section class="bg-navy-50 py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">

        {{-- B. Shipment information --}}
        @if($infoRows)
            <div class="rounded-2xl border border-navy-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Shipment Information</h2>
                <div class="mt-3 h-0.5 w-14 bg-gold-500"></div>
                <dl class="mt-6 divide-y divide-navy-100">
                    @foreach($infoRows as $label => $value)
                        <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-baseline sm:gap-6">
                            <dt class="flex w-56 shrink-0 items-center gap-2 text-sm font-semibold text-navy-800">
                                <svg class="h-4 w-4 shrink-0 text-gold-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ $label }}
                            </dt>
                            <dd class="font-medium text-gold-600 sm:flex-1">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif

        {{-- C. Receiver information (privacy-gated) --}}
        @if($payload['receiver'])
            <div class="mt-10 rounded-2xl border border-navy-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Receiver Information</h2>
                <div class="mt-3 h-0.5 w-14 bg-gold-500"></div>
                <dl class="mt-6 divide-y divide-navy-100">
                    @foreach($partyLabels as $key => $label)
                        @if(isset($payload['receiver'][$key]))
                            <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-baseline sm:gap-6">
                                <dt class="flex w-56 shrink-0 items-center gap-2 text-sm font-semibold text-navy-800">
                                    <svg class="h-4 w-4 shrink-0 text-gold-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
                                    {{ $label }}
                                </dt>
                                <dd class="font-medium text-gold-600 sm:flex-1">{{ $payload['receiver'][$key] }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
        @endif

        {{-- D. Sender information (privacy-gated) --}}
        @if($payload['sender'])
            <div class="mt-10 rounded-2xl border border-navy-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Sender Information</h2>
                <div class="mt-3 h-0.5 w-14 bg-gold-500"></div>
                <dl class="mt-6 divide-y divide-navy-100">
                    @foreach($partyLabels as $key => $label)
                        @if(isset($payload['sender'][$key]))
                            <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-baseline sm:gap-6">
                                <dt class="flex w-56 shrink-0 items-center gap-2 text-sm font-semibold text-navy-800">
                                    <svg class="h-4 w-4 shrink-0 text-gold-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
                                    {{ $label }}
                                </dt>
                                <dd class="font-medium text-gold-600 sm:flex-1">{{ $payload['sender'][$key] }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
        @endif

        {{-- E. Shipment history --}}
        @if($payload['events'])
            <div class="mt-10 rounded-2xl border border-navy-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Shipment History</h2>
                <div class="mt-3 h-0.5 w-14 bg-gold-500"></div>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-navy-100 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-navy-500">
                                <th class="pb-3 pr-4">Date</th>
                                <th class="pb-3 pr-4">Time</th>
                                <th class="pb-3 pr-4">Location</th>
                                <th class="pb-3 pr-4">Status</th>
                                <th class="pb-3">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-50">
                            @foreach($payload['events'] as $event)
                                <tr>
                                    <td class="whitespace-nowrap py-3 pr-4 font-medium text-navy-800">{{ $event['date'] ? \Carbon\Carbon::parse($event['date'])->format('d M Y') : '' }}</td>
                                    <td class="whitespace-nowrap py-3 pr-4 text-navy-600">{{ $event['time'] }}</td>
                                    <td class="py-3 pr-4 text-navy-600">{{ $event['location'] }}</td>
                                    <td class="whitespace-nowrap py-3 pr-4 font-semibold text-navy-800">{{ $event['status'] }}</td>
                                    <td class="py-3 text-navy-600">{{ $event['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- F. Live map --}}
        <div class="mt-10 overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-sm" x-data="shipmentMap(@js($payload['map']), '{{ route('track.location.json', $shipment->tracking_number) }}')">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-navy-100 px-6 py-4 sm:px-8">
                <div>
                    <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Live Shipment Map</h2>
                    <div class="mt-2 h-0.5 w-14 bg-gold-500"></div>
                </div>
                <span class="text-xs text-navy-400" x-text="updatedLabel"></span>
            </div>
            <template x-if="hasMap">
                <div id="tracking-map" class="h-80 w-full sm:h-96"></div>
            </template>
            <template x-if="!hasMap">
                <div class="flex h-40 flex-col items-center justify-center gap-2 px-6 text-center">
                    <svg class="h-8 w-8 text-navy-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm text-navy-500">No location data has been confirmed for this shipment yet.</p>
                    <p class="text-xs text-navy-400">The map will appear here once a confirmed location is recorded.</p>
                </div>
            </template>
            <p class="border-t border-navy-100 px-6 py-3 text-xs text-navy-400 sm:px-8">
                Locations shown are the latest recorded updates from authorised systems or our operations team — this view does not stream continuous GPS.
            </p>
        </div>

        @if($shipment->image)
            <div class="mt-10 rounded-2xl border border-navy-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="font-display text-xl font-bold uppercase tracking-wide text-navy-900">Package Image</h2>
                <div class="mt-3 h-0.5 w-14 bg-gold-500"></div>
                <img src="{{ asset('storage/'.$shipment->image) }}" alt="Shipment item" class="mt-6 w-full max-w-md rounded-xl object-cover" loading="lazy">
            </div>
        @endif

        @if($shipment->public_notes)
            <div class="mt-10 rounded-2xl border border-gold-200 bg-gold-50 p-6">
                <p class="text-sm leading-6 text-navy-800">{{ $shipment->public_notes }}</p>
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('contact', ['tracking' => $shipment->tracking_number]) }}" class="inline-flex items-center gap-2 rounded-xl bg-navy-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-navy-900/20 transition hover:bg-navy-800">
                <svg class="h-4 w-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 10a6 6 0 00-12 0v3a2 2 0 002 2h1a2 2 0 002-2v-1a2 2 0 00-2-2H8m4 11v-3a2 2 0 012-2h1"/></svg>
                Questions about this shipment? Contact support
            </a>
            <p class="mt-3 text-xs text-navy-400">Live chat is available via the support widget when our team is online.</p>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
function shipmentMap(initial, feedUrl) {
    return {
        map: null, layer: null, hasMap: !!initial, updatedLabel: '',
        feedUrl, timer: null,

        init() {
            if (initial) this.render(initial);
            this.updateLabel(initial?.updated_at);
            this.timer = setInterval(() => this.refresh(), 30000);
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) clearInterval(this.timer);
                else this.timer = setInterval(() => this.refresh(), 30000);
            });
        },

        refresh() {
            fetch(this.feedUrl, { headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : null)
                .then(data => {
                    if (!data) return;
                    if (data.map) { this.hasMap = true; this.$nextTick(() => this.render(data.map)); }
                    this.updateLabel(data.map?.updated_at);
                })
                .catch(() => {});
        },

        render(payload) {
            this.$nextTick(() => {
                if (!this.map) {
                    this.map = L.map('tracking-map', { scrollWheelZoom: false });
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19,
                    }).addTo(this.map);
                }
                if (this.layer) this.map.removeLayer(this.layer);
                this.layer = L.layerGroup().addTo(this.map);

                const bounds = [];
                const add = (pt, color, title) => {
                    if (!pt) return;
                    const coords = '(' + Number(pt.lat).toFixed(4) + ', ' + Number(pt.lng).toFixed(4) + ')';
                    const label = (title || pt.label || '') + '<br><b>Lat/Lng:</b> ' + coords;
                    const m = L.circleMarker([pt.lat, pt.lng], {
                        radius: 8, color: '#fff', weight: 2, fillColor: color, fillOpacity: 1,
                    }).addTo(this.layer).bindPopup(label);
                    m.bindTooltip(coords, { direction: 'top', offset: [0, -8] });
                    bounds.push([pt.lat, pt.lng]);
                };

                add(payload.origin, '#748cc0', 'Departure: ' + (payload.origin?.label ?? ''));
                add(payload.destination, '#344270', 'Destination: ' + (payload.destination?.label ?? ''));
                add(payload.current, '#d4a017', 'Last confirmed: ' + (payload.current?.label ?? ''));

                // Live route: departure → recorded path points → current → destination.
                const route = [];
                const push = (pt) => {
                    if (!pt) return;
                    const ll = [pt.lat, pt.lng];
                    const last = route[route.length - 1];
                    if (!last || last[0] !== ll[0] || last[1] !== ll[1]) route.push(ll);
                };
                push(payload.origin);
                (payload.path || []).forEach(p => push({ lat: p[0], lng: p[1] }));
                push(payload.current);
                push(payload.destination);

                if (route.length > 1) {
                    L.polyline(route, { color: '#d4a017', weight: 3, dashArray: '6 8', opacity: 0.85 }).addTo(this.layer);
                }

                if (bounds.length === 1) this.map.setView(bounds[0], 10);
                else if (bounds.length > 1) this.map.fitBounds(bounds, { padding: [30, 30] });
            });
        },

        updateLabel(ts) {
            this.updatedLabel = ts ? ('Updated ' + new Date(ts).toLocaleString()) : '';
        },
    };
}
</script>
@endpush
@endsection
