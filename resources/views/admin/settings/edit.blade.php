@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Brand</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-navy-800">Company name</label>
                        <input name="company_name" value="{{ old('company_name', $settings['general']['company_name']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Tagline</label>
                        <input name="company_tagline" value="{{ old('company_tagline', $settings['general']['company_tagline']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Company description (SEO meta)</label>
                        <textarea name="company_description" rows="2" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('company_description', $settings['general']['company_description']) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Logo</label>
                        @if($logo)<img src="{{ asset('storage/'.$logo) }}" alt="Current logo" class="mb-2 h-10 rounded bg-navy-950 p-1">@endif
                        <input type="file" name="company_logo" accept="image/*" class="w-full text-sm text-navy-600 file:mr-3 file:rounded-lg file:border-0 file:bg-navy-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-navy-800">
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Contact &amp; Support</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-navy-800">Contact email</label>
                        <input name="contact_email" type="email" value="{{ old('contact_email', $settings['contact']['contact_email']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Support phone</label>
                        <input name="support_phone" value="{{ old('support_phone', $settings['contact']['support_phone']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Office address</label>
                        <input name="office_address" value="{{ old('office_address', $settings['contact']['office_address']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Business hours</label>
                        <input name="business_hours" value="{{ old('business_hours', $settings['contact']['business_hours']) }}" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Footer about text</label>
                        <textarea name="footer_about" rows="3" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('footer_about', $settings['content']['footer_about']) }}</textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-navy-700">
                        <input type="checkbox" name="notifications_enabled" value="1" @checked(old('notifications_enabled', $settings['notifications']['notifications_enabled']) === '1' || old('notifications_enabled', $settings['notifications']['notifications_enabled']) === true) class="rounded border-navy-300 text-gold-500 focus:ring-gold-500">
                        Email notifications enabled (requires configured mail settings in .env)
                    </label>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-navy-950">Legal Pages</h2>
                <p class="mt-1 text-xs text-navy-500">Rendered on the public site at /privacy-policy, /terms and /shipping-policy. Line breaks are preserved.</p>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-navy-800">Privacy Policy</label>
                        <textarea name="privacy_policy" rows="10" class="mt-1 w-full rounded-lg border-navy-200 font-mono text-xs shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('privacy_policy', $settings['content']['privacy_policy']) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Terms &amp; Conditions</label>
                        <textarea name="terms_and_conditions" rows="10" class="mt-1 w-full rounded-lg border-navy-200 font-mono text-xs shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('terms_and_conditions', $settings['content']['terms_and_conditions']) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-navy-800">Shipping Policy</label>
                        <textarea name="shipping_policy" rows="10" class="mt-1 w-full rounded-lg border-navy-200 font-mono text-xs shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('shipping_policy', $settings['content']['shipping_policy']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Public tracking privacy --}}
    <div class="mt-6 rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-navy-950">Public Tracking Page Privacy</h2>
        <p class="mt-1 text-xs text-navy-500">Choose which sender and receiver fields appear on public tracking results. Unchecked fields are never rendered. Contact details and street addresses are sensitive — keep them off unless the business requires disclosure.</p>
        <div class="mt-5 grid gap-8 lg:grid-cols-2">
            @foreach(['receiver' => $publicReceiverFields, 'sender' => $publicSenderFields] as $party => $enabled)
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-navy-700">{{ ucfirst($party) }} fields shown publicly</h3>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach($partyFields as $field)
                            <label class="flex items-center gap-2 text-sm text-navy-700">
                                <input type="checkbox" name="public_{{ $party }}_fields[]" value="{{ $field }}"
                                       @checked(in_array($field, old('public_'.$party.'_fields', $enabled) ?? [], true))
                                       class="rounded border-navy-300 text-gold-500 focus:ring-gold-500">
                                {{ ucwords(str_replace('_', ' ', $field)) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6 max-w-xs">
            <label class="text-sm font-medium text-navy-800">Shipment history order</label>
            <select name="tracking_history_order" class="mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500">
                <option value="desc" @selected(old('tracking_history_order', $historyOrder) === 'desc')>Newest event first</option>
                <option value="asc" @selected(old('tracking_history_order', $historyOrder) === 'asc')>Oldest event first</option>
            </select>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="rounded-lg bg-gold-400 px-8 py-3 font-semibold text-navy-950 hover:bg-gold-300">Save Settings</button>
    </div>
</form>

<div class="mt-8 rounded-xl border border-navy-100 bg-navy-50 p-5 text-sm text-navy-600">
    <h3 class="font-semibold text-navy-800">Environment-only settings</h3>
    <p class="mt-1">Secrets stay in <code class="rounded bg-white px-1 ring-1 ring-navy-200">.env</code>: <code>SMARTSUPP_KEY</code> (live chat), <code>MAIL_*</code> (SMTP), <code>DB_*</code>. They are never editable from this panel.</p>
</div>
@endsection
