
@extends('frontend.layouts.app')

@section('content')
    <div class="admin-seo-as-blog-container">

        <!-- BLOG GRID -->
        <div class="admin-seo-as-blog-grid">

            @forelse ($blogs as $blog)
                <div class="admin-seo-as-blog-card">
                    <a href="{{ request()->getSchemeAndHttpHost() }}/blog/{{ $blog->slug }}">
                      <img src="{{ asset('public/uploads/images/featured_image/' . $blog->featured_image) }}">
                    </a>
                    <div class="admin-seo-as-blog-content">
                        <span class="admin-seo-as-category">Art & Gallery</span>
                        <h3>
                            <a href="{{ request()->getSchemeAndHttpHost() }}/blog/{{ $blog->slug }}">
                                {{ $blog->title }}
                                
                            </a>
                            </h3>
                            
                        <div class="admin-seo-as-meta">Oct 10, 2024 • 6 Comments</div>
                        <p>{{ $blog->meta_description }}</p>
                        <!--<a href="{{ route('blog.detail', $blog->slug) }}" class="admin-seo-as-btn">Learn More →</a>-->
                        
                       <a href="{{ request()->getSchemeAndHttpHost() }}/blog/{{ $blog->slug }}" class="admin-seo-as-btn">
                            Learn More →
                        </a>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        
        

        <!-- SIDEBAR -->
        <aside class="admin-seo-as-sidebar">
            
            <div class="blog-custom-sidebar22211">
                 <div class="admin-seo-as-widget">
                    <h4>Search</h4>
                    <form action="{{ route('blog.frontend') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search">
                    </form>
                </div>
    
                <div class="admin-seo-as-widget" style="margin-top: 20px;">
                    <h4>Latest Post</h4>
    
                    @foreach ($latestBlogs as $item)
                        <div class="admin-seo-as-latest-post">
                            <img src="{{ asset('public/uploads/images/featured_image/' . $item->fetured_image) }}">
                            <div>
                                <a href="{{ url('blog/'.$item->slug) }}" style="text-decoration: none; color: #1A1A1A">{{ $item->title }}</a><br>
                                <small>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                    
                    
    
                </div>
    
                <div class="admin-seo-as-widget" style="margin-top: 20px;">
                    <h4>Categories</h4>
                    <ul>
                        <li>
                            <a style="text-decoration: none; color: #1A1A1A" href="{{ route('blog.frontend') }}"
                                class="admin-seo-as-{{ request('category') ? '' : 'active' }}">
                                All
                            </a>
                        </li>
    
                        @foreach ($categories as $cat)
                            <li>
                                <a style="text-decoration: none; color: #1A1A1A"
                                    href="{{ route('blog.frontend', ['category' => $cat->id]) }}"
                                    class="admin-seo-as-{{ request('category') == $cat->id ? 'active' : '' }}">
                                    {{ $cat->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
            </div>

           

        </aside>
        
        <div class="" style="display: flex; justify-content: center;">
                  @if ($blogs->lastPage() > 1)
                <ul class="pagination">
            
                    @if ($blogs->currentPage() > 1)
                        <li>
                            <a href="{{ $blogs->previousPageUrl() }}">Previous</a>
                        </li>
                    @endif
            
                    @for ($i = 1; $i <= $blogs->lastPage(); $i++)
                        <li class="{{ $blogs->currentPage() == $i ? 'active' : '' }}">
                            <a href="{{ $blogs->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
            
                    @if ($blogs->hasMorePages())
                        <li>
                            <a href="{{ $blogs->nextPageUrl() }}">Next</a>
                        </li>
                    @endif
            
                </ul>
            @endif
            </div>

    </div>
@endsection

@section('style')

<style>
    .pagination{
    display:flex;
    gap:8px;
    list-style:none;
    padding:0;
}

.pagination li a{
    display:block;
    padding:8px 14px;
    border:1px solid #ddd;
    border-radius:6px;
    text-decoration:none;
    color:#333;
}

.pagination li.active a{
    background: #1c2b43;
    color:#fff;
    border-color:#00a896;
}

@media (min-width: 992px) {
    .blog-custom-sidebar22211 {
        position: sticky;
        top: 11px;
        /* align-self: start; */
    }
}
</style>

@endsection

