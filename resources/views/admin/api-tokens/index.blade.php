@extends('layouts.admin')

@section('title', 'API Tokens')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-navy-950">New Integration Token</h2>
        <p class="mt-1 text-xs text-navy-500">Tokens authenticate GPS devices / courier integrations against <code class="rounded bg-navy-50 px-1">POST /api/v1/shipments/{tracking}/location</code> with the <code class="rounded bg-navy-50 px-1">update-location</code> ability.</p>
        <form method="POST" action="{{ route('admin.api-tokens.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-navy-800">Token name *</label>
                <input name="name" required placeholder="e.g. Fleet GPS unit 7" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Expires</label>
                <input name="expires_at" type="date" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <button type="submit" class="rounded-lg bg-gold-400 px-5 py-2.5 text-sm font-semibold text-navy-950 hover:bg-gold-300">Generate Token</button>
        </form>
        <div class="mt-6 rounded-lg bg-navy-50 p-4 text-xs leading-5 text-navy-600">
            <p class="font-semibold text-navy-800">Usage</p>
            <pre class="mt-2 overflow-auto">curl -X POST {{ url('/api/v1/shipments/ACE-XXXXXX/location') }} \
  -H "Authorization: Bearer &lt;token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"latitude":51.5,"longitude":-0.12,"location_name":"London hub"}'</pre>
        </div>
    </div>

    <div class="rounded-xl border border-navy-100 bg-white shadow-sm lg:col-span-2">
        <h2 class="border-b border-navy-100 px-6 py-4 font-semibold text-navy-950">Active Tokens</h2>
        <table class="w-full text-left text-sm">
            <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                <tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Created by</th><th class="px-6 py-3">Last used</th><th class="px-6 py-3">Expires</th><th class="px-6 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-navy-50">
                @forelse($tokens as $token)
                    <tr>
                        <td class="px-6 py-3 font-medium text-navy-900">{{ $token->name }}</td>
                        <td class="px-6 py-3 text-navy-600">{{ $token->tokenable?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-xs text-navy-500">{{ $token->last_used_at?->diffForHumans() ?? 'never' }}</td>
                        <td class="px-6 py-3 text-xs text-navy-500">{{ $token->expires_at?->toFormattedDateString() ?? 'never' }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('admin.api-tokens.destroy', $token) }}" onsubmit="return confirm('Revoke this token?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Revoke</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-navy-400">No tokens issued yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-navy-100 px-6 py-3">{{ $tokens->links() }}</div>
    </div>
</div>
@endsection
