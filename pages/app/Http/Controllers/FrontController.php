<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Blog, Tag, Category, Page};

class FrontController extends Controller
{
    // public function blog(Request $request)
    // {
    //     $query = Blog::query();

    //     if ($request->search) {
			
    //         $query->where(function ($q) use ($request) {
    //             $q->where('title', 'LIKE', '%' . $request->search . '%')
    //                 ->orWhere('body', 'LIKE', '%' . $request->search . '%');
    //         });

    //     }

    //     if ($request->category) {
    //         $query->where('category_id', $request->category);
    //     }

    //     $blogs = $query->latest()->paginate(10)->appends($request->all());
    //     $categories = Category::all();
    //     $latestBlogs = Blog::latest()->take(3)->get();

    //     return view('frontend.blog', compact('blogs', 'categories', 'latestBlogs'));
    // }

	public function blog(Request $request)
{
    $search = trim(strtolower($request->search));
    $category = $request->category;

    $query = Blog::query();

    // 🔍 Case-insensitive search
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(body) LIKE ?', ["%{$search}%"])
			  ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$search}%"])
			  ->orWhereRaw('LOWER(meta_description) LIKE ?', ["%{$search}%"])
		      ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$search}%"])
			  ->orWhereRaw('LOWER(meta_title) LIKE ?', ["%{$search}%"]);
        });
    }

    // 📂 Category filter
    if (!empty($category)) {
        $query->where('category_id', $category);
    }

    // 📃 Fetch blogs
    $blogs = $query->latest()
                   ->paginate(10)
                   ->withQueryString();

    $categories = Category::all();
    $latestBlogs = Blog::latest()->take(3)->get();

    return view('frontend.blog', compact(
        'blogs',
        'categories',
        'latestBlogs',
        'search',
        'category'
    ));
}



    public function blogDetail(Request $request)
    {
        $blog = Blog::where('slug', $request->slug)->firstOrFail();
        $latestBlogs = Blog::latest()->take(3)->get();
        $categories = Category::all();

        return view('frontend.blogdetail', compact('blog', 'latestBlogs', 'categories'));
    }

    public function page(Request $request)
    {
        $p = Page::where('slug', $request->slug)->where('status', 'active')->firstOrFail();

        // dd($page);

        return view('frontend.page', compact('p'));
    }
}
