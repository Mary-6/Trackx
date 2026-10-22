@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $cards = [
        ['Total Shipments', $stats['total'], 'navy'],
        ['Created Today', $stats['created_today'], 'sky'],
        ['Awaiting Dispatch', $stats['awaiting_dispatch'], 'indigo'],
        ['In Transit', $stats['in_transit'], 'cyan'],
        ['Out for Delivery', $stats['out_for_delivery'], 'violet'],
        ['Delivered', $stats['delivered'], 'green'],
        ['Delayed', $stats['delayed'], 'orange'],
        ['Exceptions / On Hold', $stats['exceptions'], 'red'],
        ['Customers', $stats['customers'], 'teal'],
        ['Open Enquiries', $stats['unresolved_enquiries'], 'amber'],
    ];
@endphp

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
    @foreach($cards as [$label, $value])
        <div class="rounded-xl border border-navy-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-navy-400">{{ $label }}</p>
            <p class="mt-2 font-display text-3xl font-semibold text-navy-950">{{ number_format($value) }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 grid gap-6 xl:grid-cols-3">
    {{-- Recent shipments --}}
    <div class="rounded-xl border border-navy-100 bg-white shadow-sm xl:col-span-2">
        <div class="flex items-center justify-between border-b border-navy-100 px-6 py-4">
            <h2 class="font-semibold text-navy-950">Recent Shipments</h2>
            <a href="{{ route('admin.shipments.index') }}" class="text-sm font-medium text-navy-600 hover:text-gold-600">View all &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                    <tr>
                        <th class="px-6 py-3">Tracking #</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Destination</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-50">
                    @forelse($recentShipments as $shipment)
                        <tr class="cursor-pointer hover:bg-navy-50/60" onclick="window.location='{{ route('admin.shipments.show', $shipment) }}'">
                            <td class="px-6 py-3"><a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-xs font-semibold text-navy-800 hover:text-gold-600">{{ $shipment->tracking_number }}</a></td>
                            <td class="px-6 py-3 text-navy-600">{{ $shipment->category?->name }}</td>
                            <td class="px-6 py-3 text-navy-600">{{ $shipment->destination_city }}, {{ $shipment->destination_country }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$shipment->status" size="sm" /></td>
                            <td class="px-6 py-3 text-xs text-navy-400">{{ $shipment->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-navy-400">No shipments yet. <a href="{{ route('admin.shipments.create') }}" class="font-semibold underline">Create the first one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Status distribution --}}
    <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-navy-950">Status Distribution</h2>
        <ul class="mt-4 space-y-2.5">
            @foreach($distribution->where('total', '>', 0) as $row)
                <li class="flex items-center justify-between text-sm">
                    <span class="text-navy-700">{{ $row->name }}</span>
                    <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-semibold text-navy-800">{{ $row->total }}</span>
                </li>
            @endforeach
            @if($distribution->where('total', '>', 0)->isEmpty())
                <li class="text-sm text-navy-400">No shipments recorded yet.</li>
            @endif
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    {{-- Recent tracking updates --}}
    <div class="rounded-xl border border-navy-100 bg-white shadow-sm">
        <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Recent Tracking Updates</h2>
        <ul class="divide-y divide-navy-50">
            @forelse($recentEvents as $event)
                <li class="flex items-start justify-between gap-4 px-6 py-3">
                    <div>
                        <a href="{{ route('admin.shipments.show', $event->shipment_id) }}" class="font-mono text-xs font-semibold text-navy-700 hover:text-gold-600">{{ $event->shipment?->tracking_number }}</a>
                        <p class="mt-0.5 text-sm text-navy-800">{{ $event->status?->name }}@if($event->location_name) — {{ $event->location_name }}@endif</p>
                        @if($event->public_message)<p class="text-xs text-navy-500">{{ $event->public_message }}</p>@endif
                    </div>
                    <time class="shrink-0 text-xs text-navy-400">{{ $event->occurred_at?->diffForHumans() }}</time>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-navy-400">No tracking events yet.</li>
            @endforelse
        </ul>
    </div>

    {{-- Recent admin activity --}}
    <div class="rounded-xl border border-navy-100 bg-white shadow-sm">
        <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Recent Administrative Activity</h2>
        <ul class="divide-y divide-navy-50">
            @forelse($recentActivity as $log)
                <li class="flex items-center justify-between gap-4 px-6 py-3 text-sm">
                    <div>
                        <span class="font-medium text-navy-800">{{ $log->user?->name ?? 'System' }}</span>
                        <span class="text-navy-500"> {{ str_replace('.', ' ', $log->action) }}</span>
                    </div>
                    <time class="text-xs text-navy-400">{{ $log->created_at->diffForHumans() }}</time>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-navy-400">No administrative activity recorded yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
