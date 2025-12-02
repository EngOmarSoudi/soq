<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function show($slug)
    {
        $page = StaticPage::where('slug', $slug)
            ->active()
            ->firstOrFail();

        return view('static-pages.show', compact('page'));
    }
}