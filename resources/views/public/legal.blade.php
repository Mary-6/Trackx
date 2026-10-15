@extends('layouts.public')

@section('title', $title . ' — ' . setting('company_name'))

@section('content')
<section class="bg-navy-950 py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <h1 class="font-display text-4xl font-semibold text-white">{{ $title }}</h1>
    </div>
</section>
<section class="bg-white py-14">
    <div class="prose prose-slate mx-auto max-w-4xl px-4 sm:px-6" style="max-width: 56rem;">
        @if($content)
            {!! nl2br(e($content)) !!}
        @else
            <p class="text-navy-500">This page has not been configured yet. Please check back later or contact us.</p>
        @endif
    </div>
</section>
@endsection
