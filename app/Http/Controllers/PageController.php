<?php

namespace App\Http\Controllers;

use App\Models\ShipmentCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'categories' => ShipmentCategory::active()->take(8)->get(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function services(): View
    {
        return view('public.services', [
            'categories' => ShipmentCategory::active()->get(),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function privacy(): View
    {
        return view('public.legal', [
            'title' => 'Privacy Policy',
            'content' => setting('privacy_policy'),
            'key' => 'privacy_policy',
        ]);
    }

    public function terms(): View
    {
        return view('public.legal', [
            'title' => 'Terms and Conditions',
            'content' => setting('terms_and_conditions'),
            'key' => 'terms_and_conditions',
        ]);
    }

    public function shippingPolicy(): View
    {
        return view('public.legal', [
            'title' => 'Shipping Policy',
            'content' => setting('shipping_policy'),
            'key' => 'shipping_policy',
        ]);
    }

    public function robots(): Response
    {
        $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /track/\nDisallow: /login\nDisallow: /profile\n\nSitemap: ".url('/sitemap.xml')."\n";

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap(): Response
    {
        $urls = ['/', '/track', '/services', '/about', '/contact', '/faq', '/privacy-policy', '/terms', '/shipping-policy'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .collect($urls)->map(fn ($u) => '<url><loc>'.e(url($u)).'</loc></url>')->implode('')
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
