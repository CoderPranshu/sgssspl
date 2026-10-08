<?php

namespace App\Http\Controllers;

use App\Models\{Category, Site};
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = Site::latest()->get();
        $categories = Category::latest()->get();
        return view('backend.category.index', compact('sites', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'site_id' => 'required',
            'status' => 'required'
        ]);

        // dd($data);

        Category::create($data);

        return back()->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // dd($category);
        $sites = Site::latest()->get();

        return view('backend.category.edit', compact('category','sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
         $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'site_id' => 'required',
            'status' => 'required'
        ]);

        // dd($data);

        $category->update($data);

        return redirect()->route('category.index')->with('success', 'Category update successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Category deleted successfully');
    }
}
