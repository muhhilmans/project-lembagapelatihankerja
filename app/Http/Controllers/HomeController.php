<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $blogs = Blog::published()->limit(6)->orderBy('published_at', 'desc')->get();

        return view('landing.index', compact('blogs'));
    }

    public function allBlogs()
    {
        $blogs = Blog::published()->orderBy('published_at', 'desc')->paginate(10);

        $blogLatest = Blog::published()->orderBy('published_at', 'desc')->limit(3)->get();

        $tags = Blog::published()->pluck('tags')->toArray();
        $tagsArray = [];

        foreach ($tags as $tag) {
            $decodedTags = json_decode($tag, true);
            foreach ($decodedTags as $item) {
                $tagsArray[] = $item['value'];
            }
        }

        $popularTags = collect($tagsArray)
            ->countBy()
            ->sortDesc()
            ->take(10);

        return view('landing.pages.blog', compact(['blogs', 'blogLatest', 'popularTags']));
    }

    public function blogDetail($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        $blogs = Blog::published()->orderBy('published_at', 'desc')->limit(3)->get();

        return view('landing.pages.blog-detail', compact(['blog', 'blogs']));
    }
}
