<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function setTheme(Request $request)
    {
        $theme = $request->input('theme', 'light');
        
        if (!in_array($theme, ['light', 'dark'])) {
            return response()->json(['error' => 'Invalid theme', 'success' => false], 422);
        }
        
        // Save to session
        session(['theme' => $theme]);
        
        // Save to user preferences if authenticated
        if (auth()->check()) {
            $preferences = auth()->user()->preferences ?? [];
            $preferences['theme'] = $theme;
            auth()->user()->update(['preferences' => $preferences]);
        }
        
        return response()->json(['success' => true, 'theme' => $theme]);
    }
}
