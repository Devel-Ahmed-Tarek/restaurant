<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function forgetCache(): void
    {
        Cache::forget('settings.all');
    }

    /**
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::query()
                ->pluck('value', 'key')
                ->map(fn ($v) => $v === null ? null : (string) $v)
                ->all();
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = static::allCached();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        static::forgetCache();
    }

    /**
     * @param  array<string, string|null>  $pairs
     */
    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            static::query()->updateOrCreate(
                ['key' => $k],
                ['value' => $v]
            );
        }
        static::forgetCache();
    }
}
