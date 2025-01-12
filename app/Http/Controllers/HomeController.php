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

        return view('landing.pages.blog', compact('blogs'));
    }

    public function blogDetail($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        return view('landing.pages.blog-detail', compact('blog'));
    }
}
