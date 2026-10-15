@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-3">
    <input name="q" value="{{ $q }}" placeholder="Search name, email, phone…" class="w-72 rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
    <label class="flex items-center gap-2 text-sm text-navy-600">
        <input type="checkbox" name="duplicates" value="1" @checked(request()->boolean('duplicates')) class="rounded border-navy-300 text-gold-500 focus:ring-gold-500"> Flagged duplicates
    </label>
    <button type="submit" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700">Search</button>
    @can('export', App\Models\Customer::class)
        <a href="{{ route('admin.customers.export') }}" class="ml-auto rounded-lg border border-navy-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">Export CSV</a>
    @endcan
</form>

<div class="overflow-hidden rounded-xl border border-navy-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Location</th><th class="px-5 py-3">Shipments</th><th class="px-5 py-3">Flags</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-navy-50">
                @forelse($customers as $customer)
                    <tr class="hover:bg-navy-50/60">
                        <td class="px-5 py-3 font-medium text-navy-900">{{ $customer->name }}</td>
                        <td class="px-5 py-3 text-navy-600">{{ $customer->email ?? '—' }}</td>
                        <td class="px-5 py-3 text-navy-600">{{ collect([$customer->city, $customer->country])->filter()->join(', ') ?: '—' }}</td>
                        <td class="px-5 py-3 text-navy-600">{{ $customer->shipments_count }}</td>
                        <td class="px-5 py-3">@if($customer->is_flagged_duplicate)<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">duplicate?</span>@endif</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="font-semibold text-navy-700 hover:text-gold-600">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-navy-400">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-navy-100 px-5 py-3">{{ $customers->links() }}</div>
</div>
@endsection
