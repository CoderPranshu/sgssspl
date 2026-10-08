@extends('frontend.layouts.app')

@section('content')

<!-- HERO -->

<div class="admin-seo-as-hero">
    <div class="admin-seo-as-hero-content">
        <h1>{{ $blog->title }}</h1>
        <p>Home > Blog > {{ $blog->title }}</p>
    </div>
</div>

<div class="admin-seo-as-wrapper">
    <div class="admin-seo-as-container">

    <!-- LEFT CONTENT -->
    <div class="admin-seo-as-blog-detail">

        <span class="admin-seo-as-category">{{ $blog->category->name ?? 'General' }}</span>

        <h2>{{ $blog->title }}</h2>

        <div class="admin-seo-as-meta">
            {{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y') }}
        </div>

        <!-- IMAGE -->
        <!--<img src="{{ asset('public/uploads/images/featured_image/' . $blog->fetured_image) }}" alt="{{ $blog->title }}">-->

        <!-- BODY -->
        <div class="admin-seo-as-blog-body">
            {!! $blog->body !!}
        </div>

    </div>

    <!-- SIDEBAR -->
    <aside class="admin-seo-as-sidebar">
        <div class="blog-custom-sidebar22211">
              <!-- SEARCH -->
        <div class="admin-seo-as-widget">
            <h4>Search</h4>
            <form action="{{ route('blog.frontend') }}" method="GET">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search">
            </form>
        </div>

        <!-- LATEST -->
        <div class="admin-seo-as-widget" style="margin-top: 20px;">
            <h4>Latest Post</h4>

            @foreach ($latestBlogs as $item)
                <div class="admin-seo-as-latest">
                    <!--<img src="{{ asset('public/uploads/images/featured_image/' . $item->fetured_image) }}">-->
                    <img src="{{ url('pages/public/uploads/images/featured_image/' . $item->fetured_image) }}">
                    <div>
                        <a href="{{ url('blog/'.$item->slug) }}" style="text-decoration:none;color:#1A1A1A;">
                            {{ $item->title }}
                        </a><br>
                        <small>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- CATEGORIES -->
        <div class="admin-seo-as-widget" style="margin-top: 20px;">
            <h4>Categories</h4>
            <ul>
                <li>
                    <a href="{{ route('blog.frontend') }}">
                        All
                    </a>
                </li>

                @foreach ($categories as $cat)
                    <li>
                        <a href="{{ route('blog.frontend', ['category' => $cat->id]) }}">
                            {{ $cat->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

            
        </div>

      
    </aside>

</div>

</div>

@stop

@section('style')

<style>


.ctaone h3 span,
.ctaone p span {
    color: #000000 !important;
}

.ctaone,
.ctaone * {
    color: #000 !important;
}

/* HERO FIXED */
.admin-seo-as-hero {
    padding: 100px 20px 60px;
    min-height: 350px;
    /*background: url('https://images.unsplash.com/photo-1772289934881-182ad9578f8c?q=80&w=1032') center/cover;*/
    background-color: #1a2948;
    
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-top: 40px;
}

.admin-seo-as-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
}

.admin-seo-as-hero-content {
    position: relative;
    z-index: 2;
    color: #fff;
    text-align: center;
}

.admin-seo-as-hero-content h1 {
    font-size: 32px;
    color: #fff !important;
    margin-bottom: 10px;
}

.admin-seo-as-hero-content p {
    font-size: 14px;
    color: #fff !important;
    opacity: 0.9;
}

/* WRAPPER (prevents theme conflicts) */
.admin-seo-as-wrapper {
    width: 100%;
}

/* LAYOUT */
.admin-seo-as-container {
    max-width: 1200px;
    margin: auto;
    padding: 50px 20px;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

/* BLOG */
.admin-seo-as-blog-detail {
    background: #fff;
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}

.admin-seo-as-category {
    font-size: 12px;
    color: #00a896;
    text-transform: uppercase;
    font-weight: 600;
}

.admin-seo-as-blog-detail h2 {
    font-size: 26px;
    margin: 10px 0;
}

.admin-seo-as-meta {
    font-size: 13px;
    color: #888;
    margin-bottom: 15px;
}

.admin-seo-as-blog-detail img {
    width: 100%;
    border-radius: 14px;
    margin-bottom: 20px;
}

.admin-seo-as-blog-body p {
    font-size: 15px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 15px;
}

@media (min-width: 992px) {
    .blog-custom-sidebar22211 {
        position: sticky;
        top: 11px;
        /* align-self: start; */
    }
}
/* SIDEBAR */
/*.admin-seo-as-sidebar {*/
/*    display: flex;*/
/*    flex-direction: column;*/
/*    gap: 25px;*/
/*}*/

.admin-seo-as-widget {
    background: #fff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}

.admin-seo-as-widget h4 {
    font-size: 18px;
    margin-bottom: 15px;
}

.admin-seo-as-widget input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 10px;
}

/* LATEST */
.admin-seo-as-latest {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.admin-seo-as-latest img {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
}

/* RESPONSIVE */
@media(max-width:992px) {
    .admin-seo-as-container {
        grid-template-columns: 1fr;
    }
}

/* add css by Nikesh kumar jha */

/* ===== BASIC RESET ===== */
*,::after,::before{box-sizing:border-box;}
body{margin:0;font-family:"Poppins",sans-serif;background:#f4f4f4;}
img{max-width:100%;height:auto;}
a{text-decoration:none;}

/* ===== BOOTSTRAP-LIKE GRID (USED BY FOOTER) ===== */
.container{width:100%;margin:auto;padding:0 15px;}
.row{display:flex;flex-wrap:wrap;}
.col-lg-4,.col-md-6,.col-md-12{width:100%;}

@media(min-width:768px){
.col-md-6{width:50%;}
.col-md-12{width:100%;}
}
@media(min-width:992px){
.col-lg-4{width:33.333%;}
}

.text-center{text-align:center;}
.position-absolute{position:absolute;}
.end-0{right: 20px;}
.mb-0{margin-bottom:0;}
.align-items-start{align-items:flex-start;}

/* ===== FOOTER STYLES ===== */
.footer-global{
background:radial-gradient(circle at 20% 30%, #111 0%, #2B4E84 100%);
color:#ccc;
padding:50px 0 20px;
position:relative;
}
.footer-global::before{
content:"";
position:absolute;
top:-80px;
left:50%;
transform:translateX(-50%);
width:140px;
height:140px;
background:url('https://globalliving.dk/css/images/globe-icon.svg') no-repeat center;
background-size:contain;
opacity:.05;
}
.footer-content-global{
border-bottom:1px solid rgba(255,255,255,.1);
padding-bottom:20px;
}
.footer-brand-global p{
font-size:15px;
line-height:1.7;
}
.footer-links-global h4,
.footer-contact-global h4{
color:#fff;
font-size:18px;
margin-bottom:18px;
}
.footer-links-global ul{
list-style:none;
padding:0;
margin:0;
}
.footer-links-global li{margin-bottom:8px;}
.footer-links-global a,
.footer-contact-global a{
color:#ccc;
transition:.3s;
}
.footer-links-global a:hover,
.footer-contact-global a:hover{
color:#ffb400;
}
.footer-contact-global p{
font-size:15px;
margin-bottom:6px;
}
.footer-social{
display:flex;
align-items:center;
margin-top:10px;
}
.footer-linkedin-icon{
width:26px;
filter:brightness(0) invert(1);
transition:.3s;
}
.footer-social a:hover img{
transform:translateY(-2px);
}
.footer-policy-link a{
display:flex;
align-items:center;
gap:8px;
color:#ffffffcc;
}
.footer-bottom-global{
background:#000;
padding:15px 0;
font-size:14px;
position:relative;
}
.company-no{bottom: 16px;}
.footer-gap{display:inline-block;width:6px;}

@media(max-width:576px){
.footer-bottom-global{text-align:center;}
.company-no{position:static;display:block;margin-top:8px;}
}

/*end css by Nikesh kumar jha*/

</style>

@stop
