@extends('layouts.admin')

@section('title', 'New Account')

@section('content')
<div class="max-w-xl rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium text-navy-800">Name *</label>
            <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Email *</label>
            <input name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Role *</label>
            <select name="role" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                @foreach(App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Password *</label>
            <input name="password" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Confirm password *</label>
            <input name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-gold-400 px-6 py-2.5 font-semibold text-navy-950 hover:bg-gold-300">Create Account</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-navy-200 px-6 py-2.5 font-semibold text-navy-700 hover:bg-navy-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
