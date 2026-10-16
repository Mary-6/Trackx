<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name' => 'Aurevia Courier Express',
            'company_tagline' => 'Precision shipping for what matters most',
            'company_description' => 'Aurevia Courier Express provides secure, transparent shipment tracking for parcels, freight and goods of every kind, worldwide.',
            'brand_accent' => 'gold',
            'contact_email' => 'aureviacourierexpress@gmail.com',
            'support_phone' => '+1 (817) 471-3896',
            'office_address' => '12 Meridian Wharf, London E14, United Kingdom',
            'business_hours' => 'Monday – Friday, 09:00 – 18:00 GMT',
            'footer_about' => 'Aurevia Courier Express provides secure, tracked transport for parcels, documents, electronics and commercial cargo worldwide.',
            'privacy_policy' => $this->defaultPrivacy(),
            'terms_and_conditions' => $this->defaultTerms(),
            'shipping_policy' => $this->defaultShipping(),
            'notifications_enabled' => '1',
            'public_receiver_fields' => '["name","city_state","country"]',
            'public_sender_fields' => '["name","city_state","country"]',
            'tracking_history_order' => 'desc',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function defaultPrivacy(): string
    {
        return <<<'TXT'
Effective date: 2026

Aurevia Courier Express ("we", "us", "our") respects the privacy of every sender, recipient and website visitor. This policy explains what we collect, why we collect it, and how it is protected.

## Information we collect
- Shipment details: sender and recipient names, contact details, addresses, origin and destination, package description, weight and declared contents.
- Account details: names, email addresses and roles of staff and integration partners.
- Technical data: IP addresses, browser type and access logs, used for security, rate limiting and abuse prevention.
- Enquiries: messages submitted through the contact form or live chat.

## How we use your information
- To create, manage and track shipments and provide proof of delivery.
- To send shipment notifications where configured.
- To respond to support enquiries.
- To meet legal, customs and regulatory obligations.

## Public tracking pages
Anyone holding a valid tracking number can view the shipment status, confirmed locations and event history for that shipment. By default we never display sender or receiver contact details, street-level addresses, or internal notes on public pages. Administrators may enable additional fields where a business need requires it.

## Sharing
We do not sell personal data. Information is shared only with carriers, customs authorities and delivery partners as required to complete a shipment, or where the law requires it.

## Retention
Shipment records are retained for as long as needed for operational, legal and insurance purposes. Contact messages are kept for a reasonable support period.

## Security
Access to shipment data is role-restricted, audited and transmitted over HTTPS. Private notes and contact details are never exposed through public endpoints.

## Your rights
You may request access, correction or deletion of your personal information by contacting us at the email address on our Contact page, subject to legal retention requirements.

## Cookies
We use only session cookies required for website functionality and any cookies set by the live-chat provider, if enabled.
TXT;
    }

    private function defaultTerms(): string
    {
        return <<<'TXT'
Effective date: 2026

By using the Aurevia Courier Express website and tracking services you agree to the following terms.

## Service description
This website provides shipment tracking and support information for consignments handled by Aurevia Courier Express. Tracking information reflects the latest confirmed updates recorded in our systems or received from authorised integrations.

## Tracking numbers
Tracking numbers grant visibility of a shipment's status and history. They are issued per consignment — keep them private and share only with parties who need to follow the shipment.

## Acceptable use
You may not attempt to access shipments that do not belong to you, probe or disrupt our systems, scrape the tracking service at scale, or use the contact and chat channels for unlawful purposes. Lookups are rate-limited and abusive traffic may be blocked.

## Accuracy of information
Estimated delivery dates are estimates, not guarantees, and may change due to customs, weather, carrier performance or other factors outside our control. Confirmed statuses and locations represent the best available record at the time of display.

## Carriage terms
These website terms do not replace the shipping agreement, bill of lading or contract of carriage between you and Aurevia Courier Express. Carriage liability, prohibited items and claims procedures are governed by that agreement and applicable law.

## Intellectual property
All site content, branding and the Aurevia Courier Express mark are our property and may not be reproduced without permission.

## Changes
We may update these terms at any time; the current version is always published on this page. Continued use of the site constitutes acceptance.
TXT;
    }

    private function defaultShipping(): string
    {
        return <<<'TXT'
Effective date: 2026

## What we ship
Documents, personal parcels, electronics, fashion, commercial cargo, and fragile or high-value goods with enhanced handling — domestic and international.

## Tracking
Every consignment receives a unique tracking number (format ACE-XXXXXX) which can be entered on our tracking page for the latest confirmed status, event history and last confirmed location. Location updates reflect genuine recorded data from our operations team or authorised tracking integrations — never simulated movement.

## Transit times and delivery estimates
Estimated delivery dates are displayed on the tracking page once confirmed by our team. International shipments may be subject to customs clearance delays outside our control. Estimates are indicative and not guaranteed.

## Restrictions and prohibited items
We do not carry prohibited or restricted goods as defined by local, international or carrier regulations, including illegal substances, hazardous materials without declaration, weapons, currency and perishables without prior arrangement. Declared contents must be accurate; mis-declared shipments may be refused, held or reported.

## High-value and fragile goods
Enhanced-handling shipments can include tamper-evident packaging, signature-on-delivery and declared-value insurance arranged before dispatch. Contact support for coverage terms and limits.

## Delivery
Delivery is made to the recipient address recorded at booking. A signature or proof of delivery may be required. If a delivery attempt fails, the shipment may be held, re-attempted or returned to sender according to the shipping agreement.

## Claims
Loss or damage claims must be raised within the period stated in your shipping agreement, with supporting evidence of contents and value.
TXT;
    }
}
