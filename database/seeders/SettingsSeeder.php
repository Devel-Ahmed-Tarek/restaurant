<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Foodlay',
            'currency_symbol' => 'EGP',
            'primary_color' => '#e91e63',
            'meta_description' => '',
            'meta_keywords' => '',
            'contact_phone' => '',
            'whatsapp_number' => '',
            'contact_email' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'twitter_url' => '',
            'tiktok_url' => '',
            'youtube_url' => '',
            'google_analytics_id' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Setting::forgetCache();
    }
}
