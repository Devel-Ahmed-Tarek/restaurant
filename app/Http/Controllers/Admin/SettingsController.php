<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => Setting::allCached(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'currency_symbol' => 'nullable|string|max:20',
            'meta_description' => 'nullable|string|max:2000',
            'meta_keywords' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:32',
            'contact_email' => 'nullable|email|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'tiktok_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'primary_color' => ['nullable', 'regex:/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'google_analytics_id' => 'nullable|string|max:32',
            'logo' => 'nullable|image|max:4096',
            'favicon' => 'nullable|file|mimes:ico,png,svg|max:1024',
            'og_image' => 'nullable|image|max:4096',
            'remove_logo' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
            'remove_og_image' => 'nullable|boolean',
        ]);

        $disk = Storage::disk('public');

        $updates = [
            'site_name' => $validated['site_name'] ?? null,
            'currency_symbol' => $validated['currency_symbol'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'tiktok_url' => $validated['tiktok_url'] ?? null,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'google_analytics_id' => $validated['google_analytics_id'] ?? null,
        ];

        if (! empty($validated['primary_color'])) {
            $c = $validated['primary_color'];
            $updates['primary_color'] = str_starts_with($c, '#') ? $c : '#'.$c;
        }

        if ($request->boolean('remove_logo')) {
            $old = Setting::get('site_logo');
            if ($old) {
                $disk->delete($old);
            }
            $updates['site_logo'] = null;
        } elseif ($request->hasFile('logo')) {
            $old = Setting::get('site_logo');
            if ($old) {
                $disk->delete($old);
            }
            $updates['site_logo'] = $request->file('logo')->store('site', 'public');
        }

        if ($request->boolean('remove_favicon')) {
            $old = Setting::get('site_favicon');
            if ($old) {
                $disk->delete($old);
            }
            $updates['site_favicon'] = null;
        } elseif ($request->hasFile('favicon')) {
            $old = Setting::get('site_favicon');
            if ($old) {
                $disk->delete($old);
            }
            $updates['site_favicon'] = $request->file('favicon')->store('site', 'public');
        }

        if ($request->boolean('remove_og_image')) {
            $old = Setting::get('og_image');
            if ($old) {
                $disk->delete($old);
            }
            $updates['og_image'] = null;
        } elseif ($request->hasFile('og_image')) {
            $old = Setting::get('og_image');
            if ($old) {
                $disk->delete($old);
            }
            $updates['og_image'] = $request->file('og_image')->store('site', 'public');
        }

        foreach ($updates as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', __('Settings saved.'));
    }
}
