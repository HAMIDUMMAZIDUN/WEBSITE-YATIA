<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Gallery;
use App\Models\SocialMedia;
use App\Models\Banner;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $articles = Article::where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();
        $galleries = Gallery::latest()->take(8)->get();
        $socials = SocialMedia::where('is_active', true)->get();
        $profile = SchoolProfile::firstOrCreate(['id' => 1]);

        return view('welcome', compact('banners', 'articles', 'galleries', 'socials', 'profile'));
    }
}
