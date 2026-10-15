@extends('layouts.admin')

@section('title', 'Users & Roles')

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-gold-400 px-4 py-2 text-sm font-semibold text-navy-950 hover:bg-gold-300">+ New Account</a>
</div>

<div class="overflow-hidden rounded-xl border border-navy-100 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
            <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-navy-50">
            @foreach($users as $user)
                <tr class="hover:bg-navy-50/60">
                    <td class="px-5 py-3 font-medium text-navy-900">{{ $user->name }}</td>
                    <td class="px-5 py-3 text-navy-600">{{ $user->email }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->isSuperAdmin() ? 'bg-gold-400/15 text-gold-600' : 'bg-navy-50 text-navy-700' }}">{{ $user->roleLabel() }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-navy-700 hover:text-gold-600">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="border-t border-navy-100 px-5 py-3">{{ $users->links() }}</div>
</div>
@endsection
