@extends('layouts.admin')

@section('title', 'Customer Enquiries')

@section('content')
<div class="space-y-4">
    @forelse($enquiries as $enquiry)
        <div class="rounded-xl border p-5 shadow-sm {{ $enquiry->is_resolved ? 'border-navy-100 bg-white opacity-60' : 'border-amber-200 bg-amber-50/40' }}">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-semibold text-navy-950">{{ $enquiry->subject ?: 'General enquiry' }}
                        @if($enquiry->is_resolved)<span class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">resolved</span>@endif
                    </p>
                    <p class="mt-0.5 text-xs text-navy-500">{{ $enquiry->name }} &lt;{{ $enquiry->email }}&gt; — {{ $enquiry->created_at->diffForHumans() }}</p>
                    @if($enquiry->tracking_number)
                        <p class="mt-1 text-xs">Re: shipment <a class="font-mono font-semibold text-navy-700 underline" href="{{ route('track.result', $enquiry->tracking_number) }}">{{ $enquiry->tracking_number }}</a></p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.enquiries.toggle', $enquiry) }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-navy-200 bg-white px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">
                        {{ $enquiry->is_resolved ? 'Reopen' : 'Mark resolved' }}
                    </button>
                </form>
            </div>
            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-navy-700">{{ $enquiry->message }}</p>
        </div>
    @empty
        <div class="rounded-xl border border-navy-100 bg-white p-10 text-center text-sm text-navy-400">No enquiries yet.</div>
    @endforelse
</div>
<div class="mt-6">{{ $enquiries->links() }}</div>
@endsection
