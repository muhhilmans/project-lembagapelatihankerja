<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $blogs = Blog::limit(6)->orderBy('created_at', 'desc')->get();

        return view('landing.index', compact('blogs'));
    }

    public function allBlogs()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(10);

        $blogLatest = Blog::orderBy('created_at', 'desc')->limit(3)->get();

        $tags = Blog::all()->pluck('tags')->toArray();
        $tagsArray = [];

        foreach ($tags as $tag) {
            $decodedTags = json_decode($tag, true); // Decode JSON tags
            foreach ($decodedTags as $item) {
                $tagsArray[] = $item['value']; // Ambil value saja
            }
        }

        $popularTags = collect($tagsArray)
            ->countBy() // Hitung jumlah setiap tag
            ->sortDesc() // Urutkan berdasarkan jumlah terbanyak
            ->take(10);

        return view('landing.pages.blog', compact(['blogs', 'blogLatest', 'popularTags']));
    }

    public function blogDetail($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $blogs = Blog::orderBy('created_at', 'desc')->limit(3)->get();

        return view('landing.pages.blog-detail', compact(['blog', 'blogs']));
    }
}
