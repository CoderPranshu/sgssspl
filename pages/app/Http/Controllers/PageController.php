<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Services\Page\PageHtmlAssetScanner;
use Illuminate\Support\Facades\File;
// use App\Models\{Blog}

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('backend.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PageHtmlAssetScanner $scanner)
    {
        $request->merge(['content_mode' => $request->input('content_mode', 'normal')]);

        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'content_mode' => 'nullable|in:normal,custom',
            'body' => 'nullable|required_if:content_mode,normal',
            'custom_html' => 'nullable|required_if:content_mode,custom',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'swiper_enabled' => 'nullable|boolean',
            'head_option' => 'nullable',
            'status' => 'required',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'breadcrum_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'header_option' => 'nullable',

            // FAQ Validation
            'faq_question' => 'nullable|array',
            'faq_question.*' => 'nullable|string',

            'faq_answer' => 'nullable|array',
            'faq_answer.*' => 'nullable|string',
        ]);

        $data['content_mode'] = $data['content_mode'] ?? 'normal';
        $data['body'] = $data['body'] ?? '';
        $data['swiper_enabled'] = $request->boolean('swiper_enabled');

        // Upload Image
        if ($request->hasFile('image')) {

            $imagename = bin2hex(random_bytes(16)) . '.' . $request->image->extension();

            $request->image->move(
                public_path('uploads/images/page/'),
                $imagename
            );

            $data['image'] = $imagename;
        }

        // Upload Breadcrumb Image
        if ($request->hasFile('breadcrum_image')) {

            $breadimage = bin2hex(random_bytes(16)) . '.' . $request->breadcrum_image->extension();

            $request->breadcrum_image->move(
                public_path('uploads/image/page/'),
                $breadimage
            );

            $data['breadcrum_image'] = $breadimage;
        }

        /*
    |--------------------------------------------------------------------------
    | Create Page
    |--------------------------------------------------------------------------
    */

        $page = Page::create($data);
        $this->syncCustomAssets($page, $scanner);

        /*
    |--------------------------------------------------------------------------
    | Store FAQs
    |--------------------------------------------------------------------------
    */

        if ($request->faq_question && $request->faq_answer) {

            foreach ($request->faq_question as $key => $question) {

                $answer = $request->faq_answer[$key] ?? '';

                // Skip Empty Rows
                if (empty($question) && empty($answer)) {
                    continue;
                }

                $page->faqs()->create([
                    'faq_question' => $question,
                    'faq_answer'   => $answer,
                ]);
            }
        }

        if ($page->content_mode === 'custom') {
            $assetCount = $page->assets()->where('status', '!=', 'unused')->count();
            return redirect()->to(route('pages.edit', $page) . '?assets=1')
                ->with('success', 'Custom page created. ' . $assetCount . ' image slot(s) detected. Upload the required images below.');
        }

        return redirect()
            ->route('pages.index')
            ->with('success', 'Created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('backend.pages.edit', compact('page'));
    }

    /**
     * Authenticated preview, including inactive/draft pages.
     */
    public function preview(Page $page)
    {
        $p = $page;
        $isPreview = true;
        return view('frontend.page', compact('p', 'isPreview'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page, PageHtmlAssetScanner $scanner)
    {
        $request->merge(['content_mode' => $request->input('content_mode', 'normal')]);

        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'content_mode' => 'nullable|in:normal,custom',
            'body' => 'nullable|required_if:content_mode,normal',
            'custom_html' => 'nullable|required_if:content_mode,custom',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'swiper_enabled' => 'nullable|boolean',
            'head_option' => 'nullable',
            'status' => 'required',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],

            'breadcrum_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'header_option' => 'nullable',

            // FAQ Validation
            'faq_id' => 'nullable|array',

            'faq_question' => 'nullable|array',
            'faq_question.*' => 'nullable|string',

            'faq_answer' => 'nullable|array',
            'faq_answer.*' => 'nullable|string',
        ]);

        $data['content_mode'] = $data['content_mode'] ?? 'normal';
        $data['body'] = $data['body'] ?? '';
        $data['swiper_enabled'] = $request->boolean('swiper_enabled');

        /*
    |--------------------------------------------------------------------------
    | Upload Image
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('image')) {

            $imagename = bin2hex(random_bytes(16)) . '.' . $request->image->extension();

            $request->image->move(
                public_path('uploads/images/page/'),
                $imagename
            );

            $data['image'] = $imagename;
        }

        /*
    |--------------------------------------------------------------------------
    | Upload Breadcrumb Image
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('breadcrum_image')) {

            $breadimage = bin2hex(random_bytes(16)) . '.' . $request->breadcrum_image->extension();

            $request->breadcrum_image->move(
                public_path('uploads/image/page/'),
                $breadimage
            );

            $data['breadcrum_image'] = $breadimage;
        }

        /*
    |--------------------------------------------------------------------------
    | Update Page
    |--------------------------------------------------------------------------
    */

        $page->update($data);
        $this->syncCustomAssets($page->fresh(), $scanner);

        /*
    |--------------------------------------------------------------------------
    | Update FAQs
    |--------------------------------------------------------------------------
    */

        // Delete old FAQs
        $page->faqs()->delete();

        // Insert new FAQs
        if ($request->faq_question && $request->faq_answer) {

            foreach ($request->faq_question as $key => $question) {

                $answer = $request->faq_answer[$key] ?? '';

                // Skip Empty Rows
                if (empty($question) && empty($answer)) {
                    continue;
                }

                $page->faqs()->create([
                    'faq_question' => $question,
                    'faq_answer'   => $answer,
                ]);
            }
        }

        if ($page->content_mode === 'custom') {
            $assetCount = $page->assets()->where('status', '!=', 'unused')->count();
            $missingCount = $page->assets()->where('status', 'missing')->count();
            return redirect()->to(route('pages.edit', $page) . '?assets=1')
                ->with('success', 'Custom page updated. ' . $assetCount . ' image slot(s) detected; ' . $missingCount . ' still need an upload.');
        }

        return redirect()
            ->route('pages.index')
            ->with('success', 'Updated successfully');
    }

    private function syncCustomAssets(Page $page, PageHtmlAssetScanner $scanner): void
    {
        if ($page->content_mode !== 'custom') {
            $page->assets()->update(['status' => 'unused']);
            return;
        }

        $found = collect($scanner->scan($page->custom_html, $page->custom_css));
        $references = $found->pluck('reference')->all();

        foreach ($found as $item) {
            $asset = $page->assets()->firstOrNew(['reference' => $item['reference']]);
            $asset->fill($item);
            if (!$asset->exists || !$asset->stored_path) {
                $asset->status = 'missing';
            } elseif ($asset->status === 'unused') {
                $asset->status = 'uploaded';
            }
            $asset->save();
        }

        $stale = $page->assets();
        if (count($references)) {
            $stale->whereNotIn('reference', $references);
        }
        $stale->update(['status' => 'unused']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $pageId = $page->id;
        $page->delete();

        // DB rows cascade; clean the page-specific files too.
        File::deleteDirectory(public_path('uploads/pages/' . $pageId));

        return redirect()->route('pages.index');
    }
}
