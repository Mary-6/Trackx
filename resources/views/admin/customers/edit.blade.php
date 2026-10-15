@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
<div class="max-w-2xl rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        @method('PUT')
        <div>
            <label class="text-sm font-medium text-navy-800">Name *</label>
            <input name="name" value="{{ old('name', $customer->name) }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Email</label>
            <input name="email" type="email" value="{{ old('email', $customer->email) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Phone</label>
            <input name="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Address</label>
            <input name="address" value="{{ old('address', $customer->address) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">City</label>
            <input name="city" value="{{ old('city', $customer->city) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Country</label>
            <input name="country" value="{{ old('country', $customer->country) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium text-navy-800">Internal notes</label>
            <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('notes', $customer->notes) }}</textarea>
        </div>
        <div class="sm:col-span-2 flex gap-3">
            <button type="submit" class="rounded-lg bg-gold-400 px-6 py-2.5 font-semibold text-navy-950 hover:bg-gold-300">Save</button>
            <a href="{{ route('admin.customers.show', $customer) }}" class="rounded-lg border border-navy-200 px-6 py-2.5 font-semibold text-navy-700 hover:bg-navy-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
