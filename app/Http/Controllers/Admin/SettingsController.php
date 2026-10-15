<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public const EDITABLE_KEYS = [
        'general' => ['company_name', 'company_tagline', 'company_description', 'brand_accent'],
        'contact' => ['contact_email', 'support_phone', 'office_address', 'business_hours'],
        'content' => ['footer_about', 'privacy_policy', 'terms_and_conditions', 'shipping_policy'],
        'notifications' => ['notifications_enabled'],
    ];

    public const PARTY_FIELDS = ['name', 'email', 'phone', 'house_number', 'street', 'city_state', 'country'];

    public function edit(): View
    {
        $settings = [];
        foreach (self::EDITABLE_KEYS as $group => $keys) {
            foreach ($keys as $key) {
                $settings[$group][$key] = SiteSetting::getValue($key);
            }
        }

        return view('admin.settings.edit', [
            'settings' => $settings,
            'logo' => SiteSetting::getValue('company_logo'),
            'partyFields' => self::PARTY_FIELDS,
            'publicReceiverFields' => (array) json_decode((string) SiteSetting::getValue('public_receiver_fields', '["name","city_state","country"]'), true),
            'publicSenderFields' => (array) json_decode((string) SiteSetting::getValue('public_sender_fields', '["name","city_state","country"]'), true),
            'historyOrder' => SiteSetting::getValue('tracking_history_order', 'desc'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'company_name' => ['nullable', 'string', 'max:120'],
            'company_tagline' => ['nullable', 'string', 'max:160'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'brand_accent' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:160'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'business_hours' => ['nullable', 'string', 'max:160'],
            'footer_about' => ['nullable', 'string', 'max:1000'],
            'privacy_policy' => ['nullable', 'string', 'max:60000'],
            'terms_and_conditions' => ['nullable', 'string', 'max:60000'],
            'shipping_policy' => ['nullable', 'string', 'max:60000'],
            'notifications_enabled' => ['nullable', 'boolean'],
            'company_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'],
            'public_receiver_fields' => ['nullable', 'array'],
            'public_receiver_fields.*' => ['in:'.implode(',', self::PARTY_FIELDS)],
            'public_sender_fields' => ['nullable', 'array'],
            'public_sender_fields.*' => ['in:'.implode(',', self::PARTY_FIELDS)],
            'tracking_history_order' => ['nullable', 'in:asc,desc'],
        ];

        $data = $request->validate($rules);

        SiteSetting::setValue('public_receiver_fields', json_encode(array_values($request->input('public_receiver_fields', []))), 'privacy');
        SiteSetting::setValue('public_sender_fields', json_encode(array_values($request->input('public_sender_fields', []))), 'privacy');
        SiteSetting::setValue('tracking_history_order', $request->input('tracking_history_order', 'desc'), 'privacy');

        if ($request->hasFile('company_logo')) {
            $old = SiteSetting::getValue('company_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            SiteSetting::setValue('company_logo', $request->file('company_logo')->store('branding', 'public'), 'general');
        }

        foreach (self::EDITABLE_KEYS as $group => $keys) {
            foreach ($keys as $key) {
                if ($key === 'notifications_enabled') {
                    SiteSetting::setValue($key, $request->boolean($key) ? '1' : '0', $group);
                } elseif (array_key_exists($key, $data)) {
                    SiteSetting::setValue($key, $data[$key], $group);
                }
            }
        }

        AuditLogger::log('settings.updated', null, null, ['keys' => array_keys($data)]);

        return back()->with('success', 'Settings saved.');
    }
}
