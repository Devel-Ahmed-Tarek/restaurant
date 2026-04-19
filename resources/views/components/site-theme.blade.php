@php
    $primary = site_setting('primary_color', '#e91e63') ?? '#e91e63';
    $palette = \App\Support\ThemePalette::fromPrimary($primary);
@endphp
<style>
    :root {
        --color-primary-50: {{ $palette['50'] }};
        --color-primary-100: {{ $palette['100'] }};
        --color-primary-200: {{ $palette['200'] }};
        --color-primary-300: {{ $palette['300'] }};
        --color-primary-400: {{ $palette['400'] }};
        --color-primary-500: {{ $palette['500'] }};
        --color-primary-600: {{ $palette['600'] }};
        --color-primary-700: {{ $palette['700'] }};
        --color-primary-800: {{ $palette['800'] }};
        --color-primary-900: {{ $palette['900'] }};
    }
</style>
