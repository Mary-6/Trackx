@extends('layouts.admin')

@section('title', 'Shipment Categories')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-navy-950">New Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-navy-800">Name *</label>
                <input name="name" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Description</label>
                <input name="description" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <button type="submit" class="rounded-lg bg-gold-400 px-5 py-2.5 text-sm font-semibold text-navy-950 hover:bg-gold-300">Add Category</button>
        </form>
    </div>

    <div class="rounded-xl border border-navy-100 bg-white shadow-sm lg:col-span-2">
        <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">All Categories</h2>
        <ul class="divide-y divide-navy-50">
            @foreach($categories as $category)
                <li class="px-6 py-4" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-medium text-navy-900">{{ $category->name }}
                                @unless($category->is_active)<span class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">inactive</span>@endunless
                            </p>
                            <p class="text-xs text-navy-500">{{ $category->description }} — {{ $category->shipments_count }} shipment(s)</p>
                        </div>
                        @can('create', App\Models\Shipment::class)
                            <button type="button" @click="editing = true" class="text-sm font-semibold text-navy-600 hover:text-gold-600">Edit</button>
                        @endcan
                    </div>
                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-wrap items-center gap-3">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $category->name }}" required class="w-56 rounded-lg border-navy-200 text-sm shadow-sm">
                        <input name="description" value="{{ $category->description }}" placeholder="Description" class="min-w-0 flex-1 rounded-lg border-navy-200 text-sm shadow-sm">
                        <label class="flex items-center gap-1.5 text-sm text-navy-600"><input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded border-navy-300 text-gold-500"> Active</label>
                        <button type="submit" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white">Save</button>
                        <button type="button" @click="editing = false" class="text-sm text-navy-500">Cancel</button>
                    </form>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-navy-100 px-6 py-3">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
