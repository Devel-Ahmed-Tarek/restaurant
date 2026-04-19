<?php

if (!function_exists('locale_route')) {
    /**
     * Generate route URL for current locale (customer routes).
     */
    function locale_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $params = array_merge(['locale' => app()->getLocale()], $parameters);
        return route($name, $params, $absolute);
    }
}

if (!function_exists('site_setting')) {
    /**
     * Site configuration (database). Safe if table is missing during install.
     */
    function site_setting(string $key, ?string $default = null): ?string
    {
        try {
            return \App\Models\Setting::get($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        $s = site_setting('currency_symbol', 'EGP');

        return $s !== null && $s !== '' ? $s : 'EGP';
    }
}

if (!function_exists('format_currency')) {
    function format_currency(float|string|int|null $amount, int $decimals = 2): string
    {
        $n = $amount === null || $amount === '' ? 0.0 : (float) $amount;

        return currency_symbol().' '.number_format($n, $decimals);
    }
}

if (!function_exists('site_logo_url')) {
    function site_logo_url(): ?string
    {
        $path = site_setting('site_logo');

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}

if (!function_exists('site_favicon_url')) {
    function site_favicon_url(): ?string
    {
        $path = site_setting('site_favicon');

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}

if (!function_exists('site_og_image_url')) {
    function site_og_image_url(): ?string
    {
        $path = site_setting('og_image');

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}

if (!function_exists('site_name')) {
    function site_name(): string
    {
        return site_setting('site_name', config('app.name', 'Foodlay')) ?? config('app.name', 'Foodlay');
    }
}
