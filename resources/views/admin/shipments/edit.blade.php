@extends('layouts.admin')

@section('title', 'Edit ' . $shipment->tracking_number)

@section('content')
<p class="mb-6 text-sm text-navy-500">Editing shipment <span class="font-mono font-semibold">{{ $shipment->tracking_number }}</span>. Tracking history is preserved — use the shipment page to add status events.</p>
<form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.shipments._form')
    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-lg bg-gold-400 px-6 py-3 font-semibold text-navy-950 hover:bg-gold-300">Save Changes</button>
        <a href="{{ route('admin.shipments.show', $shipment) }}" class="rounded-lg border border-navy-200 px-6 py-3 font-semibold text-navy-700 hover:bg-navy-50">Cancel</a>
    </div>
</form>
@endsection
