<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageAsset;
use App\Services\Page\PageHtmlAssetScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PageAssetController extends Controller
{
    public function scan(Page $page, PageHtmlAssetScanner $scanner)
    {
        abort_unless($page->content_mode === 'custom', 422, 'Asset scanning is only available for Custom HTML pages.');

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

        $stale = $page->assets()->when(count($references), fn ($q) => $q->whereNotIn('reference', $references));
        if (count($references) === 0) {
            $stale = $page->assets();
        }
        $stale->update(['status' => 'unused']);

        return redirect()->to(route('pages.edit', $page) . '?assets=1')->with('success', $found->count() . ' local image reference(s) found.');
    }

    public function upload(Request $request, Page $page, PageAsset $asset)
    {
        abort_unless($page->content_mode === 'custom', 422, 'Asset uploads are only available for Custom HTML pages.');
        abort_unless($asset->page_id === $page->id, 404);

        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ]);

        $this->storeUploadedAsset($page, $asset, $request->file('image'));

        return redirect()->to(route('pages.edit', $page) . '?assets=1')->with('success', $asset->expected_filename . ' connected successfully.');
    }

    public function bulkUpload(Request $request, Page $page)
    {
        abort_unless($page->content_mode === 'custom', 422, 'Asset uploads are only available for Custom HTML pages.');

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ]);

        $files = $request->file('images', []);
        $assets = $page->assets()
            ->whereIn('id', array_keys($files))
            ->where('status', '!=', 'unused')
            ->get()
            ->keyBy(fn ($asset) => (string) $asset->id);

        abort_unless($assets->count() === count($files), 422, 'One or more selected image slots are invalid for this page.');

        $uploaded = 0;
        foreach ($files as $assetId => $file) {
            $asset = $assets->get((string) $assetId);
            $this->storeUploadedAsset($page, $asset, $file);
            $uploaded++;
        }

        return redirect()->to(route('pages.edit', $page) . '?assets=1')
            ->with('success', $uploaded . ' image(s) connected successfully.');
    }

    public function destroy(Page $page, PageAsset $asset)
    {
        abort_unless($page->content_mode === 'custom', 422, 'Asset removal is only available for Custom HTML pages.');
        abort_unless($asset->page_id === $page->id, 404);

        if ($asset->stored_path && file_exists(public_path($asset->stored_path))) {
            @unlink(public_path($asset->stored_path));
        }

        $asset->update([
            'stored_filename' => null,
            'stored_path' => null,
            'status' => 'missing',
        ]);

        return redirect()->to(route('pages.edit', $page) . '?assets=1')->with('success', 'Uploaded image removed.');
    }
    private function storeUploadedAsset(Page $page, PageAsset $asset, $file): void
    {
        $directory = public_path('uploads/pages/' . $page->id);
        File::ensureDirectoryExists($directory);

        $base = pathinfo($asset->expected_filename, PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9._-]+/', '-', $base) ?: 'page-image';
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $storedFilename = $base . '.' . $extension;

        $counter = 2;
        while (file_exists($directory . DIRECTORY_SEPARATOR . $storedFilename) && $storedFilename !== $asset->stored_filename) {
            $storedFilename = $base . '-' . $counter . '.' . $extension;
            $counter++;
        }

        if ($asset->stored_path && file_exists(public_path($asset->stored_path))) {
            @unlink(public_path($asset->stored_path));
        }

        $file->move($directory, $storedFilename);

        $asset->update([
            'stored_filename' => $storedFilename,
            'stored_path' => 'uploads/pages/' . $page->id . '/' . $storedFilename,
            'status' => 'uploaded',
        ]);
    }

}
