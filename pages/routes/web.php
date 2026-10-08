<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{SiteController, CategoryController, TagController, BlogController, FrontController, PageController, SiteSettingController};
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PageAssetController;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('site', SiteController::class);
Route::resource('admin/category', CategoryController::class);
Route::resource('tag', TagController::class);
Route::resource('admin/blog', BlogController::class)->middleware('auth');

// PageMaker is an admin/editor surface. Public pages are served only by the slug route below.
Route::middleware(['auth', 'pagemaker'])->group(function () {
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
    Route::resource('pages', PageController::class)->except(['show']);
    Route::post('pages/upload-image', [BlogController::class, 'upload'])->name('ckeditor.upload');

    Route::post('pages/{page}/assets/scan', [PageAssetController::class, 'scan'])->name('pages.assets.scan');
    Route::post('pages/{page}/assets/bulk-upload', [PageAssetController::class, 'bulkUpload'])->name('pages.assets.bulk-upload');
    Route::post('pages/{page}/assets/{asset}/upload', [PageAssetController::class, 'upload'])->name('pages.assets.upload');
    Route::delete('pages/{page}/assets/{asset}', [PageAssetController::class, 'destroy'])->name('pages.assets.destroy');
});

Route::post('/generate-blog-ai', [BlogController::class, 'generateAI'])
    ->name('blog.generate.ai');

Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('settings.index');
Route::post('/site-settings', [SiteSettingController::class, 'update'])->name('settings.update');
Route::post('profile/update', [SiteSettingController::class, 'profileUpdate'])->name('profile.update');
Route::post('/profile/password-update', [SiteSettingController::class, 'updatePassword'])
    ->name('profile.password.update');

Route::get('/', [FrontController::class, 'blog'])->name('blog.frontend');
Route::get('blog/{slug}', [FrontController::class, 'blogDetail'])->name('blog.detail');
Route::get('{slug}', [FrontController::class, 'page'])->name('page.frontend');
