<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Geocoder
{
    /**
     * Resolve a free-text location ("Accra, Ghana") into ['lat'=>…,'lng'=>…]
     * using OpenStreetMap Nominatim. Results are cached permanently per query.
     * Returns null when unresolved or unreachable — callers must handle it.
     */
    public function resolve(?string $query): ?array
    {
        $query = trim((string) $query);
        if ($query === '' || app()->environment('testing')) {
            return null;
        }

        return Cache::rememberForever('geo:'.md5($query), function () use ($query) {
            try {
                $row = Http::timeout(8)
                    ->withHeaders(['User-Agent' => config('app.name').' tracking-app'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'limit' => 1,
                    ])->json()[0] ?? null;

                return $row ? ['lat' => (float) $row['lat'], 'lng' => (float) $row['lon']] : null;
            } catch (\Throwable $e) {
                Log::notice('Geocoding failed', ['q' => $query, 'err' => $e->getMessage()]);

                return null;
            }
        });
    }
}
