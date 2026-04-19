<?php

namespace App\Support;

/**
 * Builds Tailwind-style primary shade hex colors from a single brand color.
 */
final class ThemePalette
{
    public static function fromPrimary(string $hex): array
    {
        $hex = self::normalizeHex($hex);
        if ($hex === null) {
            return self::defaults();
        }

        $rgb = self::hexToRgb($hex);

        return [
            '50' => self::rgbToHex(self::mixRgb($rgb, [255, 255, 255], 0.93)),
            '100' => self::rgbToHex(self::mixRgb($rgb, [255, 255, 255], 0.86)),
            '200' => self::rgbToHex(self::mixRgb($rgb, [255, 255, 255], 0.72)),
            '300' => self::rgbToHex(self::mixRgb($rgb, [255, 255, 255], 0.55)),
            '400' => self::rgbToHex(self::mixRgb($rgb, [255, 255, 255], 0.35)),
            '500' => self::rgbToHex($rgb),
            '600' => self::rgbToHex(self::mixRgb($rgb, [0, 0, 0], 0.14)),
            '700' => self::rgbToHex(self::mixRgb($rgb, [0, 0, 0], 0.28)),
            '800' => self::rgbToHex(self::mixRgb($rgb, [0, 0, 0], 0.42)),
            '900' => self::rgbToHex(self::mixRgb($rgb, [0, 0, 0], 0.58)),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function defaults(): array
    {
        return [
            '50' => '#fce4ec',
            '100' => '#f8bbd9',
            '200' => '#f48fb1',
            '300' => '#f06292',
            '400' => '#ec407a',
            '500' => '#e91e63',
            '600' => '#d81b60',
            '700' => '#c2185b',
            '800' => '#ad1457',
            '900' => '#880e4f',
        ];
    }

    private static function normalizeHex(string $hex): ?string
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return null;
        }

        return '#'.strtolower($hex);
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * @param  array{0:int,1:int,2:int}  $rgb
     */
    private static function rgbToHex(array $rgb): string
    {
        $r = max(0, min(255, (int) round($rgb[0])));
        $g = max(0, min(255, (int) round($rgb[1])));
        $b = max(0, min(255, (int) round($rgb[2])));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * @param  array{0:int,1:int,2:int}  $a
     * @param  array{0:int,1:int,2:int}  $b
     */
    private static function mixRgb(array $a, array $b, float $ratio): array
    {
        $ratio = max(0.0, min(1.0, $ratio));

        return [
            $a[0] + ($b[0] - $a[0]) * $ratio,
            $a[1] + ($b[1] - $a[1]) * $ratio,
            $a[2] + ($b[2] - $a[2]) * $ratio,
        ];
    }
}
