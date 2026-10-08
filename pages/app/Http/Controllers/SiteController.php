<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Auth;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = Site::latest()->get();
        return view('backend.site.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.site.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'domain' => 'required',
            'slug' => 'required',
            'status' => 'required'
        ]);

        $data['user_id'] = Auth::user()->id;

        Site::create($data);

        return redirect()->route('site.index')->with('success', 'Site created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        // dd($site);
        return view('backend.site.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'domain' => 'required',
            'slug' => 'required',
            'status' => 'required'
        ]);

        $data['user_id'] = Auth::user()->id;

        $site->update($data);

        return redirect()->route('site.index')->with('success', 'Site updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('site.index')->with('success', 'Site deleted successfully');
    }
}
