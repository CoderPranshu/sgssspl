<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    @php
        use Illuminate\Support\Facades\DB;
        // The main website lives one level above the Laravel /pages app.
        // Always keep a trailing slash so relative paths from inc/header.php,
        // inc/footer.php and Site Settings resolve to /sgssspl/css, /sgssspl/js, etc.
        $siteBasePath = str_replace('\\', '/', str_replace('/blog', '', dirname(request()->getBaseUrl())));
        $siteBasePath = in_array($siteBasePath, ['.', '/'], true) ? '' : '/' . trim($siteBasePath, '/');
        $base = rtrim(request()->getSchemeAndHttpHost(), '/') . $siteBasePath . '/';
        //dd($base);
        function fixAssets($html, $base)
        {
            if (!$html) {
                return '';
            }

            $join = function ($path) use ($base) {
                $path = trim(html_entity_decode($path, ENT_QUOTES | ENT_HTML5));
                if ($path === '' || preg_match('#^(?:https?:)?//#i', $path) || preg_match('/^(?:data:|mailto:|tel:|javascript:|vbscript:|#)/i', $path)) {
                    return $path;
                }
                return $base . ltrim($path, '/');
            };

            $html = preg_replace_callback('/\b(src|href)=["\']([^"\']+)["\']/i', function ($m) use ($join) {
                return $m[1] . '="' . htmlspecialchars($join($m[2]), ENT_QUOTES | ENT_HTML5) . '"';
            }, $html);

            $html = preg_replace_callback('/url\(\s*(["\']?)(.*?)\1\s*\)/i', function ($m) use ($join) {
                return 'url(' . $join($m[2]) . ')';
            }, $html);

            return $html;
        }
        $head = '';
        $body = '';

        if (!empty($p->head_option)) {
            $decoded = $p->head_option;
            // dd($decoded['head_option']);
            if (json_last_error() === JSON_ERROR_NONE) {
                $head = $decoded['head_option'] ?? '';
                $body = $decoded['body'] ?? $p->body; // fallback

                // dd($body);
            }

            $head = $decoded;
            
        
        }

        $css = DB::table('site_settings')->value('css');
   
        $css = fixAssets($css, $base);
        

    @endphp
    @if (!empty($head) && is_string($head))
        {!! $head !!}
    @endif
    {!! $css !!}

    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>

		/* FIX PAGE CONTENT HYPERLINK COLOR */
.admin-seo-as-container a {
    color: #2563eb !important;
    text-decoration: underline;
}

.admin-seo-as-container a:hover {
    color: #1747c9 !important;
}

		
        /* RESET */
        .admin-seo-as-wrapper * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Jost', sans-serif;
        }

        .admin-seo-as-wrapper {
            background: #f6f7fb;
            color: #1a1a1a;
        }

        /* HERO */
        .admin-seo-as-hero {
            padding: 100px 20px 60px;
            min-height: 510px;

         background:
                linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                url('{{ !empty($p->breadcrum_image)
                    ? url("public/uploads/image/page/" . $p->breadcrum_image)
                    : url("public/uploads/image/page/breadcrumb.jpeg") }}');
                    
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;

            overflow: hidden;
        }

        /* NORMAL HEADER */
        .admin-seo-as-hero.admin-seo-as-normal-header {
            margin-top: 20px;
        }

        /* TRANSPARENT HEADER */
        .admin-seo-as-hero.admin-seo-as-transparent-header {
            margin-top: 0;
            padding-top: 160px;
        }


        /* REMOVE OLD OVERLAY */
        .admin-seo-as-hero::after {
            display: none;
        }

        .admin-seo-as-hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
        }

        .admin-seo-as-hero-content .title  {
            font-size: 42px;
            margin-bottom: 12px;
            color: #fff !important;
            font-weight: 700;
        }

        .admin-seo-as-hero-content p {
            font-size: 15px;
            opacity: 0.95;
            color: #fff !important;
        }

        /* LAYOUT */
        .admin-seo-as-container {
            max-width: 1200px;
            margin: auto;
            padding: 50px 20px;
            /* display: grid; */
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        /* BLOG */
        .admin-seo-as-blog-detail {
            background: #fff;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
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

        .admin-seo-as-blog-detail p {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 15px;
        }

        /* QUOTE */
        .admin-seo-as-quote {
            background: #f2f6f5;
            padding: 20px;
            border-left: 4px solid #00a896;
            border-radius: 10px;
            margin: 20px 0;
            font-style: italic;
        }

        /* SECTION TITLE */
        .admin-seo-as-section-title {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0 10px;
        }

        /* SIDEBAR */
        .admin-seo-as-sidebar {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .admin-seo-as-widget {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
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

        /* CTA */
        .admin-seo-as-cta {
            text-align: center;
        }

        .admin-seo-as-cta button {
            background: #00a896;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* COMMENTS */
        .admin-seo-as-comments {
            margin-top: 40px;
        }

        .admin-seo-as-comment {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .admin-seo-as-comment img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .admin-seo-as-comment-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
        }

        /* FORM */
        .admin-seo-as-comment-form input,
        .admin-seo-as-comment-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .admin-seo-as-comment-form button {
            background: #00a896;
            color: #fff;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* RESPONSIVE */
        @media(max-width:992px) {
            .admin-seo-as-container {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:768px) {

            .admin-seo-as-hero {
                min-height: 220px;
            }

            .admin-seo-as-hero.admin-seo-as-transparent-header {
                padding-top: 130px;
            }

            .admin-seo-as-hero-content h1 {
                font-size: 28px;
            }
        }
    </style>

    <style>
        .admin-seo-as-faq-section {
            width: 100%;
            padding: 70px 0px;
            background: #f8fafc;
        }

        .admin-seo-as-faq-container {
            max-width: 900px;
            margin: auto;
        }

        .admin-seo-as-faq-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-seo-as-faq-title {
            font-size: 38px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .admin-seo-as-faq-subtitle {
            font-size: 16px;
            color: #6b7280;
            margin: 0;
        }

        .admin-seo-as-faq-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .admin-seo-as-faq-item {
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .admin-seo-as-faq-item.active {
            border-color: #2563eb;
        }

        .admin-seo-as-faq-question {
            width: 100%;
            border: none;
            outline: none;
            background: none;
            padding: 24px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            text-align: left;
            transition: 0.3s;
        }

        .admin-seo-as-faq-question:hover {
            background: #f9fafb;
        }

        .admin-seo-as-faq-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .admin-seo-as-faq-item.active .admin-seo-as-faq-icon {
            transform: rotate(45deg);
        }

        .admin-seo-as-faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }

        .admin-seo-as-faq-answer-content {
            padding: 0 25px 25px;
            color: #4b5563;
            font-size: 16px;
            line-height: 1.8;
        }

        @media(max-width:768px) {

            .admin-seo-as-faq-section {
                padding: 50px 15px;
            }

            .admin-seo-as-faq-title {
                font-size: 30px;
            }

            .admin-seo-as-faq-question {
                font-size: 16px;
                padding: 20px;
            }

            .admin-seo-as-faq-answer-content {
                padding: 0 20px 20px;
                font-size: 15px;
            }
        }
    </style>

    <style>
        .admin-seo-as-contact-section {
            background: #f5f5f5;
            padding: 100px 0;
        }

        .admin-seo-as-contact-container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;

            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 80px;
            align-items: start;
        }

        /* Left Box */

        .admin-seo-as-contact-info {
            background: #fff;
            padding: 40px;
        }

        .admin-seo-as-contact-info h2 {
            font-size: 32px;
            line-height: 1.2;
            color: #001f5b;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .admin-seo-as-contact-desc {
            color: #777;
            line-height: 1.9;
            margin-bottom: 50px;
        }

        .admin-seo-as-contact-item {
            margin-bottom: 35px;
        }

        .admin-seo-as-contact-item h4 {
            color: #2563eb;
            margin-bottom: 10px;
            font-size: 30px;
            font-weight: 700;
        }

        .admin-seo-as-contact-item p {
            color: #666;
            line-height: 1.8;
            margin: 0;
        }

        /* Right Side */

        .admin-seo-as-contact-form-wrapper {
            padding-top: 40px;
        }

        .admin-seo-as-contact-form-wrapper h2 {
            font-size: 32px;
            line-height: 1.1;
            color: #001f5b;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .admin-seo-as-form-desc {
            color: #777;
            max-width: 700px;
            line-height: 1.8;
            margin-bottom: 50px;
            font-size: 18px;
        }

        /* Form Grid */

        .admin-seo-as-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .admin-seo-as-form-grid input,
        .admin-seo-as-form-grid textarea {
            width: 100%;
            border: 1px solid #ddd;
            background: #fff;
            padding: 18px 20px;
            font-size: 16px;
            outline: none;
            box-sizing: border-box;
        }

        .admin-seo-as-form-grid input {
            height: 60px;
        }

        .admin-seo-as-form-grid textarea {
            grid-column: 1 / -1;
            height: 180px;
            resize: none;
        }

        /* Button */

        .admin-seo-as-submit-btn {
            margin-top: 25px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 15px 40px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
        }

        .admin-seo-as-submit-btn:hover {
            background: #1747c9;
        }

        /* Mobile */

        @media(max-width:992px) {

            .admin-seo-as-contact-container {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .admin-seo-as-contact-form-wrapper h2 {
                font-size: 45px;
            }

            .admin-seo-as-contact-info h2 {
                font-size: 38px;
            }
        }

        @media(max-width:768px) {

            .admin-seo-as-form-grid {
                grid-template-columns: 1fr;
            }

            .admin-seo-as-contact-form-wrapper h2 {
                font-size: 36px;
            }
        }
    </style>

</head>

<body>

    {{-- ================= HEADER ================= --}}
    @php
        $headerFile = DB::table('site_settings')->value('header_path');
        $header = '';

        if ($headerFile) {
            $headerFile = ltrim($headerFile, '/');
            $path = realpath(base_path('../' . $headerFile));

            if ($path && is_file($path)) {
                ob_start();
                include $path;
                $header = ob_get_clean();
            }
        }

        // fallback
        if (!$header && file_exists(base_path('../inc/header.php'))) {
            ob_start();
            include base_path('../inc/header.php');
            $header = ob_get_clean();
        }

        $header = fixAssets($header, $base);
    @endphp

    {!! $header !!}


    {{-- ================= HERO ================= --}}
    @php
        $isTransparentHeader = $p->header_option == 'transparent';
    @endphp

    <div
        class="admin-seo-as-hero
    {{ $isTransparentHeader ? 'admin-seo-as-transparent-header' : 'admin-seo-as-normal-header' }}">

        <div class="admin-seo-as-hero-content">
            <p class="title">{{ $p->title }}</p>

            <p>
                Home > {{ $p->title }}
            </p>
        </div>

    </div>


    {{-- ================= CONTENT ================= --}}
    @if (($p->content_mode ?? 'normal') === 'custom')
        @php
            $customRenderer = app(\App\Services\Page\CustomPageRenderer::class);
            $customHtml = $customRenderer->renderHtml($p);
            $customCss = $customRenderer->scopedCss($p);
            $customJs = $customRenderer->renderJs($p);
        @endphp
        @if ($customCss)
            <style>{!! $customCss !!}</style>
        @endif
        {{-- Custom HTML owns its body layout; do not constrain it inside the legacy PageMaker container. --}}
        <div id="seo-custom-page-{{ $p->id }}" class="seo-custom-page">
            {!! $customHtml !!}
        </div>
    @else
        <div class="admin-seo-as-container">
            @if (!empty($p->image) && file_exists(public_path('uploads/images/page/' . $p->image)))
                <div class="mb-4">
                    <img src="{{ asset('public/uploads/images/page/' . $p->image) }}" alt="Page Image"
                        style="width:100%; max-height:400px; object-fit:cover; border-radius:12px;">
                </div>
            @endif
            {!! fixAssets($p->body, $base) !!}
        </div>
    @endif

    @php
        $faqs = \App\Models\PageFaq::where('page_id', $p->id)->get();
    @endphp

    @if ($faqs->count() > 0)

        <section class="admin-seo-as-faq-section">

            <div class="admin-seo-as-faq-container">

                <div class="admin-seo-as-faq-header">
                    <h2 class="admin-seo-as-faq-title">
                        Frequently Asked Questions
                    </h2>

                    <p class="admin-seo-as-faq-subtitle">
                        Find answers to common questions.
                    </p>
                </div>
                <div class="admin-seo-as-faq-list">
                    @foreach ($faqs as $index => $faq)
                        <div class="admin-seo-as-faq-item {{ $index == 0 ? 'active' : '' }}">
                            <button type="button" class="admin-seo-as-faq-question">
                                <span>
                                    {{ $faq->faq_question }}
                                </span>
                                <span class="admin-seo-as-faq-icon">
                                    +
                                </span>
                            </button>
                            <div class="admin-seo-as-faq-answer">
                                <div class="admin-seo-as-faq-answer-content">
                                    {!! nl2br(e($faq->faq_answer)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </section>

        <section class="admin-seo-as-contact-section">
            <div class="admin-seo-as-contact-container">

                <!-- Left Side -->
                <div class="admin-seo-as-contact-info">

                    <h2>Get In Touch With Us</h2>

                    <p class="admin-seo-as-contact-desc">
                        One of the main areas that I work on with my clients is shedding
                        these non-supportive beliefs and replacing accomplish their desires.
                    </p>

                    <div class="admin-seo-as-contact-item">
                        <h4>Head Office</h4>
                        <p>WZ-303, near Jath chaupal, Palam Village, Delhi, 110045</p>
                    </div>

                    <div class="admin-seo-as-contact-item">
                        <h4>Branch</h4>
                        <p>WZ-871A, Bata Chowk, Street No. 1, Palam Village, Delhi, 110045</p>
                    </div>

                    <div class="admin-seo-as-contact-item">
                        <h4>Phone</h4>
                        <p>011-49988722</p>
                        <p>+91 99904 86962</p>
                    </div>

                    <div class="admin-seo-as-contact-item">
                        <h4>Email</h4>
                        <p>support@example.com</p>
                        <p>info@example.com</p>
                    </div>

                </div>

                <!-- Right Side -->
                <div class="admin-seo-as-contact-form-wrapper">

                    <h2>We'd love to hear from you</h2>

                    <p class="admin-seo-as-form-desc">
                        Get the oars in the water and start rowing. Execution is the
                        single biggest factor in achievement, the quicker you will get
                        to the goals!
                    </p>

                    <form>

                        <div class="admin-seo-as-form-grid">

                            <input type="text" placeholder="Your Name">

                            <input type="email" placeholder="Your Email">

                            <input type="text" placeholder="Phone Number">

                            <input type="text" placeholder="Subject">

                            <textarea placeholder="Message"></textarea>

                        </div>

                        <button type="submit" class="admin-seo-as-submit-btn">
                            Submit
                        </button>

                    </form>

                </div>

            </div>

        </section>

    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const faqItems = document.querySelectorAll('.admin-seo-as-faq-item');

            faqItems.forEach(item => {

                const question = item.querySelector('.admin-seo-as-faq-question');
                const answer = item.querySelector('.admin-seo-as-faq-answer');

                // Open default active
                if (item.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }

                question.addEventListener('click', function() {

                    const isActive = item.classList.contains('active');

                    // Close all
                    faqItems.forEach(faq => {

                        faq.classList.remove('active');

                        const faqAnswer = faq.querySelector('.admin-seo-as-faq-answer');

                        faqAnswer.style.maxHeight = null;

                    });

                    // Open current
                    if (!isActive) {

                        item.classList.add('active');

                        answer.style.maxHeight = answer.scrollHeight + 'px';

                    }

                });

            });

        });
    </script>


    {{-- ================= FOOTER ================= --}}
    @php
        $footerFile = DB::table('site_settings')->value('footer_path');
        $footer = '';

        if ($footerFile) {
            $footerFile = ltrim($footerFile, '/');
            $path = realpath(base_path('../' . $footerFile));

            if ($path && is_file($path)) {
                ob_start();
                include $path;
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
        $js = DB::table('site_settings')->value('js');
        $js = fixAssets($js, $base);
    @endphp

    {!! $js !!}

    @if (($p->content_mode ?? 'normal') === 'custom')
        @if (!empty($customJs))
            <script>{!! $customJs !!}</script>
        @endif
    @endif

</body>

</html>
