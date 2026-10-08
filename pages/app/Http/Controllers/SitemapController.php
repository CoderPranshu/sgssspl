<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Page;
use App\Models\Category;
use App\Models\Blog;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        /*
        |--------------------------------------------------------------------------
        | Static Pages
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        // Category::where('status', 1)->chunk(500, function ($categories) use ($sitemap) {

        //     foreach ($categories as $category) {

        //         $sitemap->add(
        //             Url::create(route('blog.frontend', ['category' => $category->id]))
        //                 ->setLastModificationDate($category->updated_at)
        //                 ->setPriority(0.9)
        //                 ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        //         );
        //     }
        // });

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        Page::where('status', 1)->chunk(500, function ($posts) use ($sitemap) {

            foreach ($posts as $post) {

                $sitemap->add(
                    Url::create(route('page.frontend', $post->slug))
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.7)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Blog Posts
        |--------------------------------------------------------------------------
        */

        Blog::where('status', 1)->chunk(500, function ($posts) use ($sitemap) {

            foreach ($posts as $post) {

                $sitemap->add(
                    Url::create(route('blog.detail', $post->slug))
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.7)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            }
        });

        return $sitemap->toResponse(request());
    }
}
