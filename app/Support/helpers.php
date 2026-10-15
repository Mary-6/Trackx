<?php

use App\Models\SiteSetting;

if (! function_exists('setting')) {
    /**
     * Fetch a site setting from the database-backed settings store.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return SiteSetting::getValue($key, $default);
        } catch (Throwable) {
            return $default;
        }
    }
}
