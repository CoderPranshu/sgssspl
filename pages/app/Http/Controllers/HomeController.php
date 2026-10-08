<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Page;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month;

        // BLOG QUERY
        $blogQuery = Blog::query()->whereYear('created_at', $year);
        if ($month) {
            $blogQuery->whereMonth('created_at', $month);
        }

        // PAGE QUERY
        $pageQuery = Page::query()->whereYear('created_at', $year);
        if ($month) {
            $pageQuery->whereMonth('created_at', $month);
        }

        $blogs = [];
        $pages = [];

        // ✅ FIXED GROUP BY
        if (!$month) {
            $blogs = $blogQuery->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy(DB::raw('MONTH(created_at)')) // ✅ FIX
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->pluck('total', 'month')
                ->toArray();

            $pages = $pageQuery->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy(DB::raw('MONTH(created_at)')) // ✅ FIX
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->pluck('total', 'month')
                ->toArray();
        }

        // Month labels + data
        $months = [];
        $blogData = [];
        $pageData = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $blogData[] = $blogs[$i] ?? 0;
            $pageData[] = $pages[$i] ?? 0;
        }

        // Counts (respect filters)
        $currentMonthBlogs = $blogQuery->count();
        $currentMonthPages = $pageQuery->count();

        return view('home', compact(
            'months',
            'blogData',
            'pageData',
            'year',
            'month',
            'currentMonthBlogs',
            'currentMonthPages'
        ));
    }
}
