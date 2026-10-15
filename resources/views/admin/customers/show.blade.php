@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6">
        <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <h2 class="font-semibold text-navy-950">Customer</h2>
                @if($customer->is_flagged_duplicate)<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Flagged duplicate</span>@endif
            </div>
            <dl class="mt-4 space-y-2.5 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Name</dt><dd class="font-medium">{{ $customer->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Email</dt><dd class="font-medium">{{ $customer->email ?? '—' }}</dd></div>
                @if($canSeeContact)
                    <div class="flex justify-between gap-4"><dt class="text-navy-500">Phone</dt><dd class="font-medium">{{ $customer->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-navy-500">Address</dt><dd class="text-right font-medium">{{ $customer->address ?? '—' }}</dd></div>
                @else
                    <div class="flex justify-between gap-4"><dt class="text-navy-500">Phone / address</dt><dd class="text-xs italic text-navy-400">Restricted to shipment managers</dd></div>
                @endif
                <div class="flex justify-between gap-4"><dt class="text-navy-500">City / Country</dt><dd class="font-medium">{{ collect([$customer->city, $customer->country])->filter()->join(', ') ?: '—' }}</dd></div>
                @if($customer->notes)<div class="pt-1"><dt class="text-navy-500">Notes</dt><dd class="mt-1 text-navy-700">{{ $customer->notes }}</dd></div>@endif
            </dl>
            <div class="mt-5 flex gap-2">
                @can('update', $customer)<a href="{{ route('admin.customers.edit', $customer) }}" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700">Edit</a>@endcan
                @can('flag', $customer)
                    <form method="POST" action="{{ route('admin.customers.flag', $customer) }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-navy-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">{{ $customer->is_flagged_duplicate ? 'Clear flag' : 'Flag duplicate' }}</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-navy-100 bg-white shadow-sm lg:col-span-2">
        <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Shipments ({{ $customer->shipments->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                    <tr><th class="px-6 py-3">Tracking #</th><th class="px-6 py-3">Category</th><th class="px-6 py-3">Route</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Created</th></tr>
                </thead>
                <tbody class="divide-y divide-navy-50">
                    @forelse($customer->shipments->sortByDesc('created_at') as $shipment)
                        <tr class="hover:bg-navy-50/60">
                            <td class="px-6 py-3"><a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-xs font-semibold text-navy-800 hover:text-gold-600">{{ $shipment->tracking_number }}</a></td>
                            <td class="px-6 py-3 text-navy-600">{{ $shipment->category?->name }}</td>
                            <td class="px-6 py-3 text-xs text-navy-500">{{ $shipment->origin_city }} → {{ $shipment->destination_city }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$shipment->status" size="sm" /></td>
                            <td class="px-6 py-3 text-xs text-navy-500">{{ $shipment->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-navy-400">No shipments linked to this customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
