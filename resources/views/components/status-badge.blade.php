@props(['status', 'size' => 'md'])
@php
    $colors = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-300',
        'sky' => 'bg-sky-100 text-sky-700 ring-sky-300',
        'indigo' => 'bg-indigo-100 text-indigo-700 ring-indigo-300',
        'blue' => 'bg-blue-100 text-blue-700 ring-blue-300',
        'cyan' => 'bg-cyan-100 text-cyan-700 ring-cyan-300',
        'teal' => 'bg-teal-100 text-teal-700 ring-teal-300',
        'amber' => 'bg-amber-100 text-amber-800 ring-amber-300',
        'lime' => 'bg-lime-100 text-lime-700 ring-lime-300',
        'violet' => 'bg-violet-100 text-violet-700 ring-violet-300',
        'green' => 'bg-green-100 text-green-700 ring-green-300',
        'orange' => 'bg-orange-100 text-orange-800 ring-orange-300',
        'red' => 'bg-red-100 text-red-700 ring-red-300',
        'yellow' => 'bg-yellow-100 text-yellow-800 ring-yellow-300',
        'rose' => 'bg-rose-100 text-rose-700 ring-rose-300',
        'gray' => 'bg-gray-100 text-gray-600 ring-gray-300',
    ];
    $color = $status?->color ?? 'slate';
    $classes = $colors[$color] ?? $colors['slate'];
    $padding = $size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm';
@endphp
<span class="status-dot inline-flex items-center rounded-full font-medium ring-1 ring-inset {{ $padding }} {{ $classes }}">
    {{ $status?->name ?? 'Unknown' }}
</span>
