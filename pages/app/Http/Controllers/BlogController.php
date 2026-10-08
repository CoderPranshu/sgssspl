<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\{Category, Site, Tag, User};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::latest()->get();

        return view('backend.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::latest()->get();
        $sites = Site::latest()->get();
        $users = User::latest()->get();
        $tags = Tag::latest()->get();
        return view('backend.blog.create', compact('users', 'sites', 'users', 'categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //     $user = auth()->user();
        // dd($user);
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'category_id' => 'required',
            'slug' => 'required',
            'excerpt' => 'nullable',
            'body' => 'required',
            'meta_head' => 'required',
            'fetured_image' => 'required',
            'status' => 'required',
        ]);

        $data['user_id'] = auth()->user()->id;

        if ($request->has('fetured_image')) {
            $imagename = uniqid() . '.' . $request->fetured_image->getClientOriginalExtension();
            $request->fetured_image->move(public_path('uploads/images/featured_image/'), $imagename);
            $data['fetured_image'] = $imagename;
        }

        $blog = Blog::create($data);

        if ($request->tags) {
            $blog->tags()->attach($request->tags);
        }

        return redirect()->route('blog.index')->with('success', 'Blog created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        $categories = Category::latest()->get();
        $sites = Site::latest()->get();
        $users = User::latest()->get();
        $tags = Tag::latest()->get();

        // return $blog->load('tags');
        return view('backend.blog.edit', compact('users', 'sites', 'users', 'categories', 'tags', 'blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Blog $blog)
    // {
    //     $data = $request->validate([
    //         'title' => 'required',
    //         'slug' => 'required',
    //         'category_id' => 'required',
    //         'title' => 'required',
    //         'slug' => 'required',
    //         'excerpt' => 'nullable',
    //         'body' => 'required',
    //         'featured_image' => 'required',
    //         'status' => 'required',
    //         // 'site_id' => 'required',
    //         // 'user_id' => 'required'
    //     ]);

    //     if ($request->has('featured_image')) {
    //         $path = public_path('uploads/images/featured_image/' . $blog->featured_image);
    //         if (file_exists($path)) {
    //             unlink($path);
    //         }

    //         $imagename = uniqid() . '.' . $request->featured_image->getClientExtension();
    //         $request->featured_image->move(public_path('uploads/images/featured_image/'), $imagename);
    //         $data['featured_image'] = $imagename;
    //     }

    //     $blog->update($data);

    //     return redirect()->route('blog.index')->with('success', 'Blog updated successfully');
    // }
    
    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'category_id' => 'required',
            'excerpt' => 'nullable',
            'body' => 'required',
            'status' => 'required',
        ]);
    
        if ($request->hasFile('fetured_image')) {
    
            $path = public_path('uploads/images/featured_image/' . $blog->fetured_image);
    
            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }
    
            $imagename = uniqid() . '.' . $request->file('fetured_image')->getClientOriginalExtension();
    
            $request->file('fetured_image')->move(
                public_path('uploads/images/featured_image'),
                $imagename
            );
    
            $data['fetured_image'] = $imagename;
        }
    
        $blog->update($data);
    
        return redirect()->route('blog.index')
            ->with('success', 'Blog updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        // return "hello";
        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog updated successfully');
    }


    public function generateAI(Request $request)
    {
        try {

            $request->validate([
                'idea' => 'required|string|max:500'
            ]);

            $prompt = "
You are an expert SEO blog writer.

Generate a complete SEO optimized blog post for the following idea:

{$request->idea}

Return strictly valid JSON only.

JSON structure must be:

{
  \"title\": \"\",
  \"slug\": \"\",
  \"body\": \"\",
  \"meta_title\": \"\",
  \"meta_keywords\": \"\",
  \"meta_description\": \"\"
}

Requirements:
- Title should be catchy and SEO optimized
- Slug should be lowercase with hyphens only
- Body minimum 800 words
- Meta title under 60 characters
- Meta description under 160 characters
- Meta keywords comma separated
";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                "model" => "llama-3.3-70b-versatile",
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                "temperature" => 0.7,
                "response_format" => [
                    "type" => "json_object"
                ]
            ]);

            // If API failed
            if (!$response->successful()) {
                return response()->json([
                    'error'   => 'Groq API failed',
                    'details' => $response->body()
                ], 500);
            }

            $result = $response->json();

            if (!isset($result['choices'][0]['message']['content'])) {
                return response()->json([
                    'error' => 'Invalid AI response structure',
                    'full_response' => $result
                ], 500);
            }

            $content = $result['choices'][0]['message']['content'];

            $decoded = json_decode($content, true);

            if (!$decoded) {
                return response()->json([
                    'error' => 'JSON decode failed',
                    'raw_output' => $content
                ], 500);
            }

            return response()->json($decoded);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile()
            ], 500);
        }
    }
    
    
public function upload(Request $request)
{
    try {

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        $path = public_path('uploads/image/page');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file->move($path, $filename);

       return response()->json([
        'location' => url('public/uploads/image/page/' . $filename)
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}
}
