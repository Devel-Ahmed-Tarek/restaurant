@extends('layouts.admin')

@section('title', __('Site settings'))

@section('content')
<div class="max-w-4xl space-y-8">
    <div>
        <h2 class="text-xl font-bold text-gray-800">{{ __('Site settings') }}</h2>
        <p class="text-gray-500 text-sm mt-1">{{ __('Branding, currency, SEO, contact and social links.') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">{{ __('General') }}</h3>
            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Site name') }}</label>
                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Currency symbol') }}</label>
                <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'EGP') }}"
                       class="w-32 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500" maxlength="20">
                <p class="text-xs text-gray-500 mt-1">{{ __('Shown before prices across the storefront and admin.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Logo') }}</label>
                    @if(!empty($settings['site_logo']))
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ Storage::url($settings['site_logo']) }}" alt="" class="h-12 object-contain rounded border border-gray-100">
                            <label class="inline-flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300">{{ __('Remove logo') }}
                            </label>
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*" class="text-sm w-full">
                </div>
                <div>
                    <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Favicon') }}</label>
                    @if(!empty($settings['site_favicon']))
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ Storage::url($settings['site_favicon']) }}" alt="" class="h-10 w-10 object-contain rounded border border-gray-100">
                            <label class="inline-flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="remove_favicon" value="1" class="rounded border-gray-300">{{ __('Remove favicon') }}
                            </label>
                        </div>
                    @endif
                    <input type="file" name="favicon" id="favicon" accept=".ico,.png,.svg,image/png,image/svg+xml" class="text-sm w-full">
                </div>
            </div>
            <div>
                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Primary brand color') }}</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="primary_color" id="primary_color"
                           value="{{ old('primary_color', $settings['primary_color'] ?? '#e91e63') }}"
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                    <span class="text-sm text-gray-500">{{ __('Applies to buttons and highlights site-wide.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">{{ __('SEO & meta') }}</h3>
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Default meta description') }}</label>
                <textarea name="meta_description" id="meta_description" rows="3"
                          class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
            </div>
            <div>
                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Meta keywords') }}</label>
                <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500" placeholder="food, delivery, ...">
            </div>
            <div>
                <label for="og_image" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Default Open Graph image') }}</label>
                @if(!empty($settings['og_image']))
                    <div class="mb-2 flex items-center gap-3">
                        <img src="{{ Storage::url($settings['og_image']) }}" alt="" class="h-16 object-cover rounded border border-gray-100">
                        <label class="inline-flex items-center gap-2 text-sm text-red-600">
                            <input type="checkbox" name="remove_og_image" value="1" class="rounded border-gray-300">{{ __('Remove image') }}
                        </label>
                    </div>
                @endif
                <input type="file" name="og_image" id="og_image" accept="image/*" class="text-sm w-full">
            </div>
            <div>
                <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Google Analytics ID') }} (G-XXXXXXXX)</label>
                <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                       class="w-full max-w-md px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500" placeholder="G-...">
                <p class="text-xs text-gray-500 mt-1">{{ __('If set here, it overrides the value from services config when present.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">{{ __('Contact') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('WhatsApp number') }}</label>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500" placeholder="201234567890">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Country code without +, used for wa.me links.') }}</p>
                </div>
            </div>
            <div>
                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                       class="w-full max-w-md px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">{{ __('Social media') }}</h3>
            <div class="space-y-3">
                @foreach ([
                    'facebook_url' => 'Facebook URL',
                    'instagram_url' => 'Instagram URL',
                    'twitter_url' => 'X (Twitter) URL',
                    'tiktok_url' => 'TikTok URL',
                    'youtube_url' => 'YouTube URL',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">{{ __($label) }}</label>
                        <input type="url" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $settings[$field] ?? '') }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500" placeholder="https://">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                {{ __('Save settings') }}
            </button>
        </div>
    </form>
</div>
@endsection
