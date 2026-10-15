@extends('layouts.admin')

@section('title', $shipment->tracking_number)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div>
            <p class="font-mono text-xl font-bold tracking-wider text-navy-950">{{ $shipment->tracking_number }}</p>
            <p class="text-sm text-navy-500">{{ $shipment->category?->name }} — created {{ $shipment->created_at->toFormattedDateString() }}@if($shipment->creator) by {{ $shipment->creator->name }}@endif</p>
        </div>
        <x-status-badge :status="$shipment->status" />
        @if($shipment->is_archived)
            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-300">Archived{{ $shipment->archive_reason ? ': '.$shipment->archive_reason : '' }}</span>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('track.result', $shipment->tracking_number) }}" target="_blank" rel="noopener" class="rounded-lg border border-navy-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">Public page &nearr;</a>
        <a href="{{ route('admin.shipments.print', $shipment) }}" target="_blank" rel="noopener" class="rounded-lg border border-navy-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">Print</a>
        @can('update', $shipment)
            <a href="{{ route('admin.shipments.edit', $shipment) }}" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700">Edit</a>
        @endcan
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    {{-- Left column: details --}}
    <div class="space-y-6">
        <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-navy-950">Details</h2>
            <dl class="mt-4 space-y-2.5 text-sm">
                @if($shipment->title)<div class="flex justify-between gap-4"><dt class="text-navy-500">Title</dt><dd class="text-right font-medium">{{ $shipment->title }}</dd></div>@endif
                @if($shipment->product_name)<div class="flex justify-between gap-4"><dt class="text-navy-500">Product</dt><dd class="text-right font-medium">{{ $shipment->product_name }}</dd></div>@endif
                @if($shipment->quantity)<div class="flex justify-between gap-4"><dt class="text-navy-500">Quantity</dt><dd class="font-medium">{{ $shipment->quantity }}</dd></div>@endif
                @if($shipment->status_reason)<div class="flex justify-between gap-4"><dt class="text-navy-500">Status reason</dt><dd class="text-right font-medium">{{ $shipment->status_reason }}</dd></div>@endif
                @if($shipment->description)<div class="flex justify-between gap-4"><dt class="text-navy-500">Contents</dt><dd class="text-right font-medium">{{ $shipment->description }}</dd></div>@endif
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Shipping method</dt><dd class="font-medium">{{ $shipment->shipping_method ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Weight</dt><dd class="font-medium">{{ $shipment->weight_kg ? $shipment->weight_kg.' kg' : '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Dimensions</dt><dd class="font-medium">{{ $shipment->dimensions ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Customs ref</dt><dd class="font-medium">{{ $shipment->customs_reference ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Dispatch date</dt><dd class="font-medium">{{ $shipment->dispatch_date?->toFormattedDateString() ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Est. delivery</dt><dd class="font-medium {{ $shipment->isDelayed() ? 'text-orange-600' : '' }}">{{ $shipment->estimated_delivery_date?->toFormattedDateString() ?? '—' }}{{ $shipment->isDelayed() ? ' (delayed)' : '' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Delivered</dt><dd class="font-medium text-green-700">{{ $shipment->delivered_at?->toDayDateTimeString() ?? '—' }}</dd></div>
            </dl>
            @if($shipment->image)
                <img src="{{ asset('storage/'.$shipment->image) }}" alt="Shipment item" class="mt-4 w-full rounded-lg object-cover">
            @endif
        </div>

        <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-navy-950">Parties <span class="ml-1 rounded bg-navy-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-navy-400">private</span></h2>
            <dl class="mt-4 space-y-2.5 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-navy-500">Sender</dt><dd class="text-right font-medium">{{ $shipment->sender_name }}</dd></div>
                @if($shipment->sender_email)<div class="flex justify-between gap-4"><dt class="text-navy-500">Sender email</dt><dd class="font-medium">{{ $shipment->sender_email }}</dd></div>@endif
                @if($shipment->sender_phone)<div class="flex justify-between gap-4"><dt class="text-navy-500">Sender phone</dt><dd class="font-medium">{{ $shipment->sender_phone }}</dd></div>@endif
                @if($shipment->sender_address)<div class="flex justify-between gap-4"><dt class="text-navy-500">Sender addr</dt><dd class="text-right font-medium">{{ trim(collect([$shipment->sender_house_number, $shipment->sender_address])->filter()->implode(' ')) ?: $shipment->sender_address }}</dd></div>@endif
                @if($shipment->sender_city || $shipment->sender_state || $shipment->sender_country)<div class="flex justify-between gap-4"><dt class="text-navy-500">Sender locale</dt><dd class="text-right font-medium">{{ collect([$shipment->sender_city, $shipment->sender_state, $shipment->sender_country])->filter()->implode(', ') }}</dd></div>@endif
                <div class="flex justify-between gap-4 pt-2"><dt class="text-navy-500">Recipient</dt><dd class="text-right font-medium">{{ $shipment->recipient_name }}</dd></div>
                @if($shipment->recipient_email)<div class="flex justify-between gap-4"><dt class="text-navy-500">Recipient email</dt><dd class="font-medium">{{ $shipment->recipient_email }}</dd></div>@endif
                @if($shipment->recipient_phone)<div class="flex justify-between gap-4"><dt class="text-navy-500">Recipient phone</dt><dd class="font-medium">{{ $shipment->recipient_phone }}</dd></div>@endif
                @if($shipment->recipient_address || $shipment->recipient_house_number)<div class="flex justify-between gap-4"><dt class="text-navy-500">Recipient addr</dt><dd class="text-right font-medium">{{ collect([$shipment->recipient_house_number, $shipment->recipient_address])->filter()->implode(' ') }}</dd></div>@endif
                @if($shipment->recipient_city || $shipment->recipient_state || $shipment->recipient_country)<div class="flex justify-between gap-4"><dt class="text-navy-500">Recipient locale</dt><dd class="text-right font-medium">{{ collect([$shipment->recipient_city, $shipment->recipient_state, $shipment->recipient_country])->filter()->implode(', ') }}</dd></div>@endif
                @if($shipment->customer)
                    <div class="flex justify-between gap-4 pt-2"><dt class="text-navy-500">Customer record</dt><dd><a href="{{ route('admin.customers.show', $shipment->customer) }}" class="font-medium text-navy-700 underline decoration-gold-400 underline-offset-2">{{ $shipment->customer->name }}</a></dd></div>
                @endif
            </dl>
        </div>

        <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-navy-950">Notes</h2>
            <div class="mt-3 space-y-3 text-sm">
                <div><p class="text-xs font-semibold uppercase text-navy-400">Public</p><p class="mt-1 text-navy-700">{{ $shipment->public_notes ?: '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-navy-400">Private <span class="normal-case">(never shown publicly)</span></p><p class="mt-1 text-navy-700">{{ $shipment->private_notes ?: '—' }}</p></div>
            </div>
        </div>

        @if($shipment->notificationRecords->isNotEmpty())
            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Notifications</h2>
                <ul class="mt-3 space-y-2 text-xs">
                    @foreach($shipment->notificationRecords->sortByDesc('created_at') as $record)
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-navy-600">{{ $record->type }} → {{ $record->recipient }}</span>
                            <span class="rounded px-1.5 py-0.5 font-semibold {{ $record->status === 'sent' ? 'bg-green-50 text-green-700' : ($record->status === 'failed' ? 'bg-red-50 text-red-700' : 'bg-navy-50 text-navy-500') }}">{{ $record->status }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(! $shipment->is_archived && auth()->user()->can('archive', $shipment))
            <form method="POST" action="{{ route('admin.shipments.archive', $shipment) }}" class="rounded-xl border border-red-100 bg-red-50/50 p-6"
                  onsubmit="return confirm('Archive this shipment? It will disappear from public tracking.')">
                @csrf
                <h2 class="font-semibold text-red-800">Archive shipment</h2>
                <input name="archive_reason" required placeholder="Reason (required)" class="mt-3 w-full rounded-lg border-red-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400">
                <button type="submit" class="mt-3 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Archive</button>
            </form>
        @elseif($shipment->is_archived && auth()->user()->can('archive', $shipment))
            <form method="POST" action="{{ route('admin.shipments.unarchive', $shipment) }}">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-navy-200 bg-white px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">Restore shipment</button>
            </form>
        @endif
    </div>

    {{-- Right column: actions + history + map --}}
    <div class="space-y-6 xl:col-span-2">
        @can('addEvent', $shipment)
            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Add Tracking Event / Update Status</h2>
                <form method="POST" action="{{ route('admin.shipments.events.store', $shipment) }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-navy-800">Status *</label>
                        <select name="shipment_status_id" required class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" @selected($status->id === $shipment->shipment_status_id)>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Location name</label>
                        <input name="location_name" value="{{ old('location_name') }}" placeholder="City, facility…" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Occurred at</label>
                        <input name="occurred_at" type="datetime-local" value="{{ old('occurred_at') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Latitude</label>
                        <input name="latitude" type="number" step="0.0000001" value="{{ old('latitude') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Longitude</label>
                        <input name="longitude" type="number" step="0.0000001" value="{{ old('longitude') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-navy-800">Public message</label>
                        <input name="public_message" value="{{ old('public_message') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Private note</label>
                        <input name="private_note" value="{{ old('private_note') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-navy-700 sm:col-span-2">
                        <input type="checkbox" name="notify_recipient" value="1" class="rounded border-navy-300 text-gold-500 focus:ring-gold-500"> Email the recipient about this update
                    </label>
                    <div class="sm:col-span-3">
                        <button type="submit" class="rounded-lg bg-navy-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">Add Event</button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Record Confirmed Location</h2>
                <p class="mt-1 text-xs text-navy-500">Manually recorded — labelled as a staff update, not continuous GPS.</p>
                <form method="POST" action="{{ route('admin.shipments.location.update', $shipment) }}" class="mt-4 grid gap-4 sm:grid-cols-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-navy-800">Latitude *</label>
                        <input name="latitude" type="number" step="0.0000001" required value="{{ old('latitude') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Longitude *</label>
                        <input name="longitude" type="number" step="0.0000001" required value="{{ old('longitude') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Location name</label>
                        <input name="location_name" value="{{ old('location_name') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Recorded at</label>
                        <input name="recorded_at" type="datetime-local" value="{{ old('recorded_at') }}" class="mt-1 w-full rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div class="sm:col-span-4">
                        <button type="submit" class="rounded-lg bg-navy-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">Record Location</button>
                    </div>
                </form>
            </div>
        @endcan

        <div class="rounded-xl border border-navy-100 bg-white shadow-sm">
            <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Full Event History <span class="ml-1 text-xs font-normal text-navy-400">(append-only)</span></h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                        <tr><th class="px-6 py-3">When</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Location</th><th class="px-6 py-3">Message</th><th class="px-6 py-3">By</th></tr>
                    </thead>
                    <tbody class="divide-y divide-navy-50">
                        @forelse($shipment->trackingEvents->sortByDesc('occurred_at') as $event)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-3 text-xs text-navy-500">{{ $event->occurred_at?->format('d M Y H:i') }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$event->status" size="sm" /></td>
                                <td class="px-6 py-3 text-navy-600">{{ $event->location_name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <p class="text-navy-700">{{ $event->public_message ?? '—' }}</p>
                                    @if($event->private_note)<p class="mt-0.5 rounded bg-amber-50 px-1.5 py-0.5 text-xs text-amber-800">🔒 {{ $event->private_note }}</p>@endif
                                </td>
                                <td class="px-6 py-3 text-xs text-navy-500">{{ $event->user?->name ?? ucfirst($event->source) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-navy-400">No events yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($shipment->locations->isNotEmpty())
            <div class="rounded-xl border border-navy-100 bg-white shadow-sm">
                <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Location History</h2>
                <ul class="divide-y divide-navy-50 text-sm">
                    @foreach($shipment->locations->sortByDesc('recorded_at') as $loc)
                        <li class="flex items-center justify-between px-6 py-2.5">
                            <span class="font-mono text-xs text-navy-600">{{ $loc->latitude }}, {{ $loc->longitude }}</span>
                            <span class="text-navy-700">{{ $loc->location_name }}</span>
                            <span class="rounded bg-navy-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-navy-500">{{ $loc->source }}</span>
                            <time class="text-xs text-navy-400">{{ $loc->recorded_at->format('d M Y H:i') }}</time>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
