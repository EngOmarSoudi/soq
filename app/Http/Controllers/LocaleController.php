<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale($locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            return redirect()->back();
        }
        
        // Save locale to session
        session(['locale' => $locale]);
        app()->setLocale($locale);
        
        // Save to user preferences if authenticated
        if (auth()->check()) {
            $preferences = auth()->user()->preferences ?? [];
            $preferences['locale'] = $locale;
            
            // Also check if theme was saved in localStorage (passed via query parameter)
            if (request()->has('theme')) {
                $preferences['theme'] = request()->input('theme');
            }
            
            auth()->user()->update(['preferences' => $preferences]);
        }
        
        return redirect()->back();
    }
}
