@extends('layouts.admin')

@section('title', 'New Shipment')

@section('content')
<form method="POST" action="{{ route('admin.shipments.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.shipments._form')
    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-lg bg-gold-400 px-6 py-3 font-semibold text-navy-950 hover:bg-gold-300">Create Shipment</button>
        <a href="{{ route('admin.shipments.index') }}" class="rounded-lg border border-navy-200 px-6 py-3 font-semibold text-navy-700 hover:bg-navy-50">Cancel</a>
    </div>
</form>
@endsection
