<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingsHelper
{
    public static function getSettings()
    {
        return Cache::rememberForever('site_settings', function () {
            return SiteSetting::getSetting();
        });
    }

    public static function get($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings->{$key} ?? $default;
    }

    public static function getName()
    {
        return self::get('site_name', 'My Website');
    }

    public static function getEmail()
    {
        return self::get('site_email');
    }

    public static function getPhone()
    {
        return self::get('site_phone');
    }

    public static function getAddress()
    {
        return self::get('site_address');
    }

    public static function getDescription()
    {
        return self::get('site_description');
    }

    public static function getSocialLinks()
    {
        return [
            'facebook' => self::get('facebook_url'),
            'twitter' => self::get('twitter_url'),
            'instagram' => self::get('instagram_url'),
            'linkedin' => self::get('linkedin_url'),
        ];
    }
}