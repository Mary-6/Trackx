@extends('layouts.admin')

@section('title', 'Edit ' . $user->name)

@section('content')
<div class="max-w-xl rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="text-sm font-medium text-navy-800">Name *</label>
            <input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Email *</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Role *</label>
            <select name="role" required class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                @foreach(App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-2 text-sm text-navy-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-navy-300 text-gold-500 focus:ring-gold-500">
            Account active
        </label>
        <div class="border-t border-navy-100 pt-4">
            <label class="text-sm font-medium text-navy-800">New password <span class="font-normal text-navy-400">(leave blank to keep current)</span></label>
            <input name="password" type="password" autocomplete="new-password" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div>
            <label class="text-sm font-medium text-navy-800">Confirm new password</label>
            <input name="password_confirmation" type="password" autocomplete="new-password" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-gold-400 px-6 py-2.5 font-semibold text-navy-950 hover:bg-gold-300">Save</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-navy-200 px-6 py-2.5 font-semibold text-navy-700 hover:bg-navy-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
