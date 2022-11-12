<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Cache;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Cache::rememberForever(Blog::staticCacheKey(), function () {
            return Blog::leftJoin('articles as a', 'a.id', '=', 'blogs.id')->get([
                'blogs.id',
                'blogs.text',
                'a.name',
                'blogs.updated_at'
            ]);
        });
        return view('blog.index', [
            'blogs' => $blogs
        ]);
    }

    public function show(int $id)
    {
        $blog = Cache::rememberForever(Blog::staticCacheKey($id), function () use ($id) {
            return Blog::with('articles')->findOrFail($id);
        });
        return view('blog.show', [
            'blog' => $blog
        ]);
    }
}
