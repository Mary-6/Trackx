@extends('layouts.admin')

@section('title', 'Shipments')

@section('content')
{{-- Filters --}}
<form method="GET" action="{{ route('admin.shipments.index') }}" class="mb-6 rounded-xl border border-navy-100 bg-white p-4 shadow-sm">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search tracking #, name, description…" class="rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500 lg:col-span-2">
        <select name="status" class="rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
            <option value="">All statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" @selected(($filters['status'] ?? '') == $status->id)>{{ $status->name }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="destination" class="rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
            <option value="">All destinations</option>
            @foreach($destinations as $d)
                <option value="{{ $d }}" @selected(($filters['destination'] ?? '') === $d)>{{ $d }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <input name="from" type="date" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-navy-200 text-sm shadow-sm" title="From date">
            <input name="to" type="date" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-navy-200 text-sm shadow-sm" title="To date">
        </div>
    </div>
    <div class="mt-3 flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700">Filter</button>
        <a href="{{ route('admin.shipments.index') }}" class="text-sm text-navy-500 hover:text-navy-800">Reset</a>
        <label class="ml-2 flex items-center gap-2 text-sm text-navy-600">
            <input type="checkbox" name="archived" value="1" @checked($filters['archived'] ?? false) class="rounded border-navy-300 text-gold-500 focus:ring-gold-500"> Archived only
        </label>
        <div class="ml-auto flex gap-2">
            @can('export', App\Models\Shipment::class)
                <a href="{{ route('admin.shipments.export', request()->query()) }}" class="rounded-lg border border-navy-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">Export CSV</a>
            @endcan
            @can('create', App\Models\Shipment::class)
                <a href="{{ route('admin.shipments.create') }}" class="rounded-lg bg-gold-400 px-4 py-2 text-sm font-semibold text-navy-950 hover:bg-gold-300">+ New Shipment</a>
            @endcan
        </div>
    </div>
</form>

{{-- Table --}}
<div class="overflow-hidden rounded-xl border border-navy-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                <tr>
                    @php
                        $sortLink = fn ($col, $label) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => ($sort === $col && $direction === 'desc') ? 'asc' : 'desc']);
                    @endphp
                    <th class="px-5 py-3"><a href="{{ $sortLink('tracking_number', 'Tracking #') }}" class="hover:text-navy-800">Tracking # {{ $sort === 'tracking_number' ? ($direction === 'asc' ? '↑' : '↓') : '' }}</a></th>
                    <th class="px-5 py-3"><a href="{{ $sortLink('recipient_name', 'Recipient') }}" class="hover:text-navy-800">Recipient {{ $sort === 'recipient_name' ? ($direction === 'asc' ? '↑' : '↓') : '' }}</a></th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Route</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"><a href="{{ $sortLink('estimated_delivery_date', 'Est. delivery') }}" class="hover:text-navy-800">Est. Delivery {{ $sort === 'estimated_delivery_date' ? ($direction === 'asc' ? '↑' : '↓') : '' }}</a></th>
                    <th class="px-5 py-3"><a href="{{ $sortLink('created_at', 'Created') }}" class="hover:text-navy-800">Created {{ $sort === 'created_at' ? ($direction === 'asc' ? '↑' : '↓') : '' }}</a></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-navy-50">
                @forelse($shipments as $shipment)
                    <tr class="cursor-pointer hover:bg-navy-50/60 {{ $shipment->isDelayed() ? 'bg-orange-50/50' : '' }}" onclick="window.location='{{ route('admin.shipments.show', $shipment) }}'">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-xs font-semibold text-navy-800 hover:text-gold-600">{{ $shipment->tracking_number }}</a>
                            @if($shipment->is_archived)<span class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">archived</span>@endif
                        </td>
                        <td class="px-5 py-3 text-navy-700">{{ $shipment->recipient_name }}</td>
                        <td class="px-5 py-3 text-navy-600">{{ $shipment->category?->name }}</td>
                        <td class="px-5 py-3 text-xs text-navy-500">{{ $shipment->origin_city }} → {{ collect([$shipment->destination_city, $shipment->destination_country])->filter()->implode(', ') }}</td>
                        <td class="px-5 py-3">
                            <x-status-badge :status="$shipment->status" size="sm" />
                            @if($shipment->isDelayed())<span class="ml-1 text-xs font-semibold text-orange-600">delayed</span>@endif
                        </td>
                        <td class="px-5 py-3 text-xs text-navy-500">{{ $shipment->estimated_delivery_date?->toFormattedDateString() ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-navy-500">{{ $shipment->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-navy-400">No shipments match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-navy-100 px-5 py-3">
        {{ $shipments->links() }}
    </div>
</div>
@endsection
