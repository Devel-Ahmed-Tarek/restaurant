@php
    $links = array_filter([
        'Facebook' => site_setting('facebook_url'),
        'Instagram' => site_setting('instagram_url'),
        'X' => site_setting('twitter_url'),
        'TikTok' => site_setting('tiktok_url'),
        'YouTube' => site_setting('youtube_url'),
    ]);
@endphp
@if(count($links))
    <div class="max-w-7xl mx-auto px-4 py-6 border-t border-gray-100">
        <p class="text-center text-sm text-gray-500 mb-3">{{ __('Follow us') }}</p>
        <div class="flex flex-wrap justify-center gap-4">
            @foreach($links as $label => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ $label }}</a>
            @endforeach
        </div>
    </div>
@endif
