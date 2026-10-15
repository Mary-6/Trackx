@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-3">
    <select name="action" class="rounded-lg border-navy-200 text-sm shadow-sm focus:border-gold-500 focus:ring-gold-500">
        <option value="">All actions</option>
        @foreach($actions as $action)
            <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-navy-800 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700">Filter</button>
</form>

<div class="overflow-hidden rounded-xl border border-navy-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-navy-50 text-xs uppercase tracking-wider text-navy-500">
                <tr><th class="px-5 py-3">When</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Action</th><th class="px-5 py-3">Subject</th><th class="px-5 py-3">IP</th><th class="px-5 py-3">Changes</th></tr>
            </thead>
            <tbody class="divide-y divide-navy-50">
                @forelse($logs as $log)
                    <tr class="align-top hover:bg-navy-50/60">
                        <td class="whitespace-nowrap px-5 py-3 text-xs text-navy-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="px-5 py-3 font-medium text-navy-800">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3"><code class="rounded bg-navy-50 px-1.5 py-0.5 text-xs">{{ $log->action }}</code></td>
                        <td class="px-5 py-3 text-xs text-navy-500">{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</td>
                        <td class="px-5 py-3 text-xs text-navy-500">{{ $log->ip_address ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($log->new_values)
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-navy-600">details</summary>
                                    <pre class="mt-1 max-w-xs overflow-auto rounded bg-navy-50 p-2 text-[10px]">{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values], JSON_PRETTY_PRINT) }}</pre>
                                </details>
                            @else
                                <span class="text-xs text-navy-300">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-navy-400">No audit entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-navy-100 px-5 py-3">{{ $logs->links() }}</div>
</div>
@endsection
