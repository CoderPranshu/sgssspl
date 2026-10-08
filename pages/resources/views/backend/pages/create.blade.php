@extends('adminlte::page')

@section('title', 'Create Page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create Page</h1>
        {{-- <a href="{{ route('pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a> --}}
    </div>
@stop

@section('content')
    <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control bg-light"
                                        placeholder="Enter the title" value="{{ old('title') }}">
                                    @error('title')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Slug <span class="text-danger">*</span></label>
                                    <input type="text" name="slug" id="slug" placeholder="Enter the slug"
                                        class="form-control bg-light @error('slug') is-invalid @enderror" name="slug"
                                        value="{{ old('slug') }}">
                                    @error('slug')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Content Mode</label>
                                    <select name="content_mode" id="content_mode" class="form-control bg-light">
                                        <option value="normal" {{ old('content_mode', 'normal') === 'normal' ? 'selected' : '' }}>Normal Editor (existing)</option>
                                        <option value="custom" {{ old('content_mode', 'normal') === 'custom' ? 'selected' : '' }}>Custom HTML / AI Page</option>
                                    </select>
                                    <small class="text-muted">Normal keeps the current TinyMCE workflow. Custom HTML enables the Page Asset Manager. Inline SVG/MathML, document-head tags, forms and embedded frames are intentionally removed for safety; use PNG/WebP/AVIF icons/images instead.</small>
                                </div>
                            </div>

                            <div class="col-md-12" id="normalBodyWrap">
                                <div class="form-group">
                                    <label>Body <span class="text-danger">*</span></label>
                                    <textarea name="body" placeholder="Enter here..." id="editor" cols="30" rows="10" class="form-control summernote">{{ old('body') }}</textarea>
                                    @error('body')<strong class="text-danger">{{ $message }}</strong>@enderror
                                </div>
                            </div>

                            <div class="col-md-12" id="customHtmlWrap" style="display:none;">
                                <div class="form-group">
                                    <label>Custom HTML <span class="text-danger">*</span></label>
                                    <textarea name="custom_html" id="custom_html" rows="22" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste ChatGPT-generated HTML here...">{{ old('custom_html') }}</textarea>
                                    @error('custom_html')<strong class="text-danger">{{ $message }}</strong>@enderror
                                    <small class="text-muted">Use local image filenames such as hero-security.webp. After saving, scan the HTML and upload the required images from the Asset Manager.</small>
                                </div>
                                <div class="form-group">
                                    <label>Custom CSS</label>
                                    <textarea name="custom_css" id="custom_css" rows="14" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste page-specific CSS here...">{{ old('custom_css') }}</textarea>
                                    <small class="text-muted">CSS is scoped to this custom page at render time to reduce collisions with the site's header/footer/global styles.</small>
                                </div>
                                <div class="form-group">
                                    <label>Custom JavaScript</label>
                                    <textarea name="custom_js" id="custom_js" rows="14" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste page-specific JavaScript here...">{{ old('custom_js') }}</textarea>
                                    @error('custom_js')<strong class="text-danger">{{ $message }}</strong>@enderror
                                    <small class="text-muted">Custom JavaScript runs on this page. Paste JavaScript only; PageMaker adds the script wrapper automatically.</small>
                                </div>
                                <div class="form-group">
                                    <label>JS/CSS Dependencies</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="swiper_enabled" name="swiper_enabled" value="1" {{ old('swiper_enabled') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="swiper_enabled">Swiper</label>
                                    </div>
                                    <small class="text-muted">Uses the Swiper library already provided by the SGSSPL site. PageMaker does not load a second Swiper CSS/JS copy.</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Header Options</label>
                                    <textarea name="head_option" class="form-control" id="meta_editor1" cols="30" rows="10">{{ old('head_option') }}</textarea>
                                    @error('head_option')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">FAQs</h5>

                                        <button type="button" class="btn btn-primary btn-sm" id="addFaq">
                                            <i class="fa fa-plus"></i> Add FAQ
                                        </button>
                                    </div>

                                    <div id="faqWrapper">

                                        {{-- <!-- Default FAQ Row -->
                                        <div class="faq-item border rounded p-3 mb-3">
                                            <div class="form-group mb-3">
                                                <label>Question</label>
                                                <input type="text" name="faq_question[]" class="form-control"
                                                    placeholder="Enter FAQ Question">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Answer</label>
                                                <textarea name="faq_answer[]" class="form-control" rows="4" placeholder="Enter FAQ Answer"></textarea>
                                            </div>

                                            <button type="button" class="btn btn-danger btn-sm removeFaq">
                                                Remove
                                            </button>
                                        </div> --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-md-4 sticky-top">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <select name="status"
                                        class="form-control bg-light @error('status') is-invalid @enderror" id="">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Breadcrumb Image</label>
                                    <input type="file"
                                        class="form-control bg-light @error('breadcrum_image') is-invalid @enderror"
                                        name="breadcrum_image">
                                    @error('breadcrum_image')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Header Option</label>
                                    <select name="header_option"
                                        class="form-control bg-light @error('header_option') is-invalid @enderror" id="">
                                        <option value="normal">Normal</option>
                                        <option value="transparent">Transparent</option>
                                    </select>
                                    @error('header_option')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('slug').value = generateSlug(this.value);
        });
    </script>

    <script>
        tinymce.init({
            selector: 'textarea.summernote',
            plugins: 'image code table lists link',
            toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat | image link | ' +
                'export preview',
            height: 500,
            images_upload_url: '/upload-image',
            automatic_uploads: true,
            images_reuse_filename: true,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: '{{ url('/') }}',
            // setup: function(editor) {
            //     editor.on('init', function() {
            //         editor.setContent(`{!! old('content', $content ?? '') !!}`);
            //     });
            // },
            images_upload_handler: imageUploadHandler
        });
    </script>

    <script>
        function togglePageContentMode() {
            const mode = document.getElementById('content_mode').value;
            const normalWrap = document.getElementById('normalBodyWrap');
            const customWrap = document.getElementById('customHtmlWrap');
            normalWrap.style.display = mode === 'normal' ? '' : 'none';
            customWrap.style.display = mode === 'custom' ? '' : 'none';
        }
        document.getElementById('content_mode').addEventListener('change', togglePageContentMode);
        togglePageContentMode();
    </script>

    <script>
        $(document).ready(function() {

            // Add FAQ
            $('#addFaq').click(function() {

                let faqHtml = `
                <div class="faq-item border rounded p-3 mb-3">
                    <div class="form-group mb-3">
                        <label>Question</label>
                        <input type="text" name="faq_question[]" class="form-control"
                            placeholder="Enter FAQ Question">
                    </div>

                    <div class="form-group mb-3">
                        <label>Answer</label>
                        <textarea name="faq_answer[]" class="form-control" rows="4"
                            placeholder="Enter FAQ Answer"></textarea>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm removeFaq">
                        Remove
                    </button>
                </div>
            `;

                $('#faqWrapper').append(faqHtml);
            });

            // Remove FAQ
            $(document).on('click', '.removeFaq', function() {
                $(this).closest('.faq-item').remove();
            });

        });
    </script>
@stop
