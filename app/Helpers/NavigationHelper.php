<?php

namespace App\Helpers;

use App\Models\StaticPage;

class NavigationHelper
{
    public static function getStaticPagesForNavigation()
    {
        return StaticPage::active()
            ->orderBy('title')
            ->get();
    }
}