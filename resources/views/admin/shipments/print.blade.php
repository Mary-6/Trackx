<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shipment {{ $shipment->tracking_number }} — Summary</title>
    <meta name="robots" content="noindex">
    @vite(['resources/css/app.css'])
    <style>body { background: white; } @media print { .no-print { display: none; } }</style>
</head>
<body class="p-8 font-sans text-navy-900">
    <div class="mx-auto max-w-3xl">
        <div class="flex items-start justify-between border-b-2 border-navy-900 pb-4">
            <x-logo :dark="true" />
            <div class="text-right">
                <p class="font-mono text-lg font-bold">{{ $shipment->tracking_number }}</p>
                <p class="text-sm text-navy-500">Printed {{ now()->toDayDateTimeString() }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-6 text-sm">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-navy-400">From</h3>
                <p class="mt-1 font-medium">{{ $shipment->sender_name }}</p>
                <p>{{ collect([$shipment->origin_city, $shipment->origin_country])->filter()->implode(', ') }}</p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-navy-400">To</h3>
                <p class="mt-1 font-medium">{{ $shipment->recipient_name }}</p>
                <p>{{ collect([$shipment->destination_city, $shipment->destination_country])->filter()->implode(', ') }}</p>
            </div>
        </div>

        <table class="mt-6 w-full text-sm">
            <tbody class="divide-y divide-navy-100">
                <tr><td class="py-2 text-navy-500">Category</td><td class="py-2 text-right font-medium">{{ $shipment->category?->name }}</td></tr>
                <tr><td class="py-2 text-navy-500">Status</td><td class="py-2 text-right font-medium">{{ $shipment->status?->name }}</td></tr>
                @if($shipment->description)<tr><td class="py-2 text-navy-500">Contents</td><td class="py-2 text-right font-medium">{{ $shipment->description }}</td></tr>@endif
                <tr><td class="py-2 text-navy-500">Dispatch date</td><td class="py-2 text-right">{{ $shipment->dispatch_date?->toFormattedDateString() ?? '—' }}</td></tr>
                <tr><td class="py-2 text-navy-500">Estimated delivery</td><td class="py-2 text-right">{{ $shipment->estimated_delivery_date?->toFormattedDateString() ?? '—' }}</td></tr>
                <tr><td class="py-2 text-navy-500">Delivered</td><td class="py-2 text-right">{{ $shipment->delivered_at?->toDayDateTimeString() ?? '—' }}</td></tr>
                @if($shipment->weight_kg)<tr><td class="py-2 text-navy-500">Weight</td><td class="py-2 text-right">{{ $shipment->weight_kg }} kg</td></tr>@endif
                @if($shipment->customs_reference)<tr><td class="py-2 text-navy-500">Customs ref</td><td class="py-2 text-right">{{ $shipment->customs_reference }}</td></tr>@endif
            </tbody>
        </table>

        <h3 class="mt-8 text-xs font-semibold uppercase tracking-wider text-navy-400">Event History</h3>
        <table class="mt-2 w-full text-sm">
            <thead><tr class="border-b border-navy-200 text-left text-xs uppercase text-navy-400"><th class="py-2">When</th><th>Status</th><th>Location</th></tr></thead>
            <tbody class="divide-y divide-navy-100">
                @foreach($shipment->trackingEvents->sortByDesc('occurred_at') as $event)
                    <tr><td class="py-2">{{ $event->occurred_at?->format('d M Y H:i') }}</td><td>{{ $event->status?->name }}</td><td>{{ $event->location_name ?? '—' }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="no-print mt-10 text-center">
            <button onclick="window.print()" class="rounded-lg bg-navy-800 px-6 py-2.5 text-sm font-semibold text-white">Print</button>
        </div>
    </div>
</body>
</html>
