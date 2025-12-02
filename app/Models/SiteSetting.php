<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** @use HasFactory<\Database\Factories\SiteSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_email',
        'site_phone',
        'site_address',
        'site_logo',
        'site_description',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
    ];

    // Get the singleton site setting
    public static function getSetting()
    {
        return self::firstOrCreate([], [
            'site_name' => 'My Website',
        ]);
    }
}