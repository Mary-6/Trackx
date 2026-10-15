@props(['dark' => false])
@php
    $logoPath = setting('company_logo');
    $name = setting('company_name', config('app.name', 'Aurevia Courier Express'));
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    @if($logoPath)
        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $name }} logo" class="h-9 w-auto" loading="lazy">
    @else
        <svg viewBox="0 0 40 40" class="h-9 w-9" aria-hidden="true">
            <rect width="40" height="40" rx="10" fill="{{ $dark ? '#d4a017' : '#1a2547' }}"/>
            <circle cx="20" cy="20" r="10.6" fill="none" stroke="{{ $dark ? '#1a2547' : '#d4a017' }}" stroke-width="2.2"/>
            <path d="M27.5 13.9 19.6 24.8l-1.6-3.3-3.2-1.7z" fill="{{ $dark ? '#1a2547' : '#d4a017' }}"/>
            <circle cx="13.6" cy="26.4" r="1.6" fill="{{ $dark ? '#1a2547' : '#f0c75e' }}"/>
        </svg>
    @endif
    @php
        [$word, $rest] = array_pad(explode(' ', $name, 2), 2, 'Courier Express');
    @endphp
    <span class="flex flex-col leading-none">
        <span class="text-xl font-bold tracking-tight {{ $dark ? 'text-navy-950' : 'text-white' }}">{{ $word }}</span>
        <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.22em] {{ $dark ? 'text-gold-600' : 'text-gold-400' }}">{{ $rest }}</span>
    </span>
</span>
