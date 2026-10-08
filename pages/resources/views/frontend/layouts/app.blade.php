<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    @php
        use Illuminate\Support\Facades\DB;

        // Dynamic BASE (works everywhere)
        //$base = request()->getSchemeAndHttpHost() . str_replace('/blog', '', dirname(request()->getBaseUrl()));
        $base = request()->getSchemeAndHttpHost();
        
        //dd($base);
        // Function to fix ALL asset paths
        function fixAssets($html, $base)
        {
            // src / href
            $html = preg_replace('/(src|href)=["\'](?!http)([^"\']+)["\']/', '$1="' . $base . '/$2"', $html);

            // background-image: url(...)
            $html = preg_replace('/url\((?!http)([^)]+)\)/', 'url(' . $base . '/$1)', $html);

            return $html;
        }

        // HEAD
        $head = DB::table('site_settings')->value('head');

        // CSS
        $cssFromDb = DB::table('site_settings')->value('css');
        $cssFromDb = fixAssets($cssFromDb, $base);
    @endphp

    {!! $head !!}
    {!! $cssFromDb !!}

    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">


<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Jost', sans-serif;
    }

    body {
        background: #f6f7fb;
        color: #1a1a1a;
    }

    /* CONTAINER */
    .admin-seo-as-blog-container {
        max-width: 1200px;
        margin: auto;
        padding: 60px 20px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
    }

    /* GRID */
    .admin-seo-as-blog-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 35px;
    }

    /* CARD */
    .admin-seo-as-blog-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        transition: .3s ease;
        height: 560px;
    }

    .admin-seo-as-blog-card:hover {
        transform: translateY(-6px);
    }

    .admin-seo-as-blog-card img {
        width: 100%;
        /*height: 240px;*/
        object-fit: contain;
    }

    /* CONTENT */
    .admin-seo-as-blog-content {
        padding: 24px;
    }

    .admin-seo-as-category {
        font-size: 12px;
        font-weight: 600;
        color: #00a896;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* TITLE */
    .admin-seo-as-blog-content h3 {
        font-size: 22px;
        font-weight: 600;
        margin: 10px 0;
        line-height: 1.5;
    }

    /* META */
    .admin-seo-as-meta {
        font-size: 13px;
        color: #888;
        margin-bottom: 10px;
    }

    /* DESC */
    .admin-seo-as-blog-content p {
        font-size: 15px;
        color: #555;
        line-height: 1.7;
        margin-bottom: 16px;
    }

    /* BUTTON */
    .admin-seo-as-btn {
        font-size: 14px;
        font-weight: 600;
        color: #00a896;
        text-decoration: none;
    }

    /* SIDEBAR */
    .admin-seo-as-sidebar {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    /* WIDGET */
    .admin-seo-as-widget {
        background: #fff;
        padding: 22px;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    }

    .admin-seo-as-widget h4 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* SEARCH */
    .admin-seo-as-widget input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        outline: none;
    }

    /* LIST */
    .admin-seo-as-widget ul {
        list-style: none;
    }

    .admin-seo-as-widget ul li {
        font-size: 15px;
        padding: 8px 0;
        color: #444;
    }

    /* LATEST POSTS */
    .admin-seo-as-latest-post {
        display: flex;
        gap: 12px;
        margin-bottom: 14px;
    }

    .admin-seo-as-latest-post img {
        width: 65px;
        height: 65px;
        border-radius: 12px;
        object-fit: cover;
    }

    .admin-seo-as-latest-post div {
        font-size: 14px;
        font-weight: 500;
    }

    .admin-seo-as-latest-post small {
        font-size: 12px;
        color: #888;
    }

    /* CTA */
    .admin-seo-as-cta {
        text-align: center;
    }

    .admin-seo-as-cta h4 {
        font-size: 20px;
    }

    .admin-seo-as-cta p {
        font-size: 14px;
        color: #666;
        margin: 10px 0 18px;
    }

    .admin-seo-as-cta button {
        background: #00a896;
        border: none;
        padding: 12px 20px;
        color: #fff;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
    }

    .admin-seo-as-cta button:hover {
        background: #028090;
    }

    /* RESPONSIVE */
    @media(max-width:992px) {
        .admin-seo-as-blog-container {
            grid-template-columns: 1fr;
        }

        .admin-seo-as-blog-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

    @yield('style')

</head>

<body>

    {{-- ================= HEADER ================= --}}
    @php
        $headerFile = DB::table('site_settings')->value('header_path');
        $header = '';

        if ($headerFile) {
            $headerFile = ltrim($headerFile, '');
            $fullPath = realpath(base_path('..' . $headerFile));

            if ($fullPath && is_file($fullPath)) {
                ob_start();
                include $fullPath;
                $header = ob_get_clean();
            }
        }

        // fallback
       // if (!$header && file_exists(base_path('../inc/header.php'))) {
         //   ob_start();
           // include base_path('../inc/header.php');
            // $header = ob_get_clean();
        //}

        $header = fixAssets($header, $base);
    @endphp

    {!! $header !!}

    {{-- ================= CONTENT ================= --}}
    @yield('content')


    {{-- ================= FOOTER ================= --}}
    @php
        $footerFile = DB::table('site_settings')->value('footer_path');
        $footer = '';

        if ($footerFile) {
            $footerFile = ltrim($footerFile, '');
            $fullPath = realpath(base_path('..' . $footerFile));

            if ($fullPath && is_file($fullPath)) {
                ob_start();
                include $fullPath;
                $footer = ob_get_clean();
            }
        }

        // fallback
        if (!$footer && file_exists(base_path('../inc/footer.php'))) {
            ob_start();
            include base_path('../inc/footer.php');
            $footer = ob_get_clean();
        }

        $footer = fixAssets($footer, $base);
    @endphp

    {!! $footer !!}


    {{-- ================= JS ================= --}}
    @php
        $jsFromDb = DB::table('site_settings')->value('js');
        $jsFromDb = fixAssets($jsFromDb, $base);
    @endphp

    {!! $jsFromDb !!}
    
    
    <script>
          $('.navbar-toggler,.closepanel').on('click', function () { 
    jQuery("header").toggleClass("active");
  }); 

  /*-------------------------------------
  Magnific Popup
  -------------------------------------*/
	var i_type = 'image';
	$('.pbmin-lightbox-video, .pbmin-lightbox-video a, a.pbmit-lightbox').each(function(){
		if( $(this).hasClass('pbmin-lightbox-video')){ 
			i_type = 'iframe';
		} else {
			i_type = 'image';
		}  
		$(this).magnificPopup({type:i_type});
	});   

    </script>

</body>

</html>
