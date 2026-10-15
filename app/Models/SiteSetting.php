<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever('site_setting:'.$key, function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            return $row?->value ?? $default;
        });
    }

    public static function setValue(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget('site_setting:'.$key);
    }

    public static function flushCache(): void
    {
        static::pluck('key')->each(fn ($key) => Cache::forget('site_setting:'.$key));
    }
}
