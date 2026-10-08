@extends('adminlte::page')

@section('title', 'Create Page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create Page</h1>
        {{-- <a href="{{ route('pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a> --}}
    </div>
@stop

@section('content')
    <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control bg-light"
                                        placeholder="Enter the title" value="{{ $page->title }}">
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
                                        value="{{ $page->slug }}">
                                    @error('slug')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Content Mode</label>
                                    <select name="content_mode" id="content_mode" class="form-control bg-light">
                                        <option value="normal" {{ old('content_mode', $page->content_mode ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal Editor (existing)</option>
                                        <option value="custom" {{ old('content_mode', $page->content_mode ?? 'normal') === 'custom' ? 'selected' : '' }}>Custom HTML / AI Page</option>
                                    </select>
                                    <small class="text-muted">Normal keeps the current TinyMCE workflow. Custom HTML enables the Page Asset Manager. Inline SVG/MathML, document-head tags, forms and embedded frames are intentionally removed for safety; use PNG/WebP/AVIF icons/images instead.</small>
                                </div>
                            </div>

                            <div class="col-md-12" id="normalBodyWrap">
                                <div class="form-group">
                                    <label>Body <span class="text-danger">*</span></label>
                                    <textarea name="body" placeholder="Enter here..." id="editor" cols="30" rows="10" class="form-control summernote">{{ old('body', $page->body) }}</textarea>
                                    @error('body')<strong class="text-danger">{{ $message }}</strong>@enderror
                                </div>
                            </div>

                            <div class="col-md-12" id="customHtmlWrap" style="display:none;">
                                <div class="form-group">
                                    <label>Custom HTML <span class="text-danger">*</span></label>
                                    <textarea name="custom_html" id="custom_html" rows="22" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste ChatGPT-generated HTML here...">{{ old('custom_html', $page->custom_html) }}</textarea>
                                    @error('custom_html')<strong class="text-danger">{{ $message }}</strong>@enderror
                                    <small class="text-muted">Use local image filenames such as hero-security.webp. The scanner prefers data-section-name / &lt;section&gt; context and otherwise uses the nearest preceding H2/H3/H1/H4. After saving, upload the required images from the Asset Manager.</small>
                                </div>
                                <div class="form-group">
                                    <label>Custom CSS</label>
                                    <textarea name="custom_css" id="custom_css" rows="14" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste page-specific CSS here...">{{ old('custom_css', $page->custom_css) }}</textarea>
                                    <small class="text-muted">CSS is scoped to this custom page at render time to reduce collisions with the site's header/footer/global styles.</small>
                                </div>
                                <div class="form-group">
                                    <label>Custom JavaScript</label>
                                    <textarea name="custom_js" id="custom_js" rows="14" class="form-control" style="font-family:Consolas,Monaco,monospace;font-size:13px;" placeholder="Paste page-specific JavaScript here...">{{ old('custom_js', $page->custom_js) }}</textarea>
                                    @error('custom_js')<strong class="text-danger">{{ $message }}</strong>@enderror
                                    <small class="text-muted">Custom JavaScript runs on this page. Paste JavaScript only; PageMaker adds the script wrapper automatically.</small>
                                </div>
                                <div class="form-group">
                                    <label>JS/CSS Dependencies</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="swiper_enabled" name="swiper_enabled" value="1" {{ old('swiper_enabled', $page->swiper_enabled ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="swiper_enabled">Swiper</label>
                                    </div>
                                    <small class="text-muted">Uses the Swiper library already provided by the SGSSPL site. PageMaker does not load a second Swiper CSS/JS copy.</small>
                                </div>

                                @php
                                    $customActiveAssetCount = $page->assets()->where('status', '!=', 'unused')->count();
                                    $customMissingAssetCount = $page->assets()->where('status', 'missing')->count();
                                @endphp
                                <div class="alert {{ $customMissingAssetCount ? 'alert-warning' : 'alert-info' }} mb-3">
                                    <strong>Image Asset Manager:</strong>
                                    {{ $customActiveAssetCount }} image slot(s) detected,
                                    {{ $customMissingAssetCount }} waiting for upload.
                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#pageAssetManagerModal"><i class="fas fa-images"></i> Manage Images</button>
                                    <div class="small mt-1">Saving a Custom HTML page automatically scans the saved HTML. The manual scan button below is only for re-checking.</div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Header Options</label>
                                    <textarea name="head_option" class="form-control" id="meta_editor1" cols="30" rows="10">{{ $page->head_option }}</textarea>
                                    @error('head_option')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
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

                                            @forelse($page->faqs as $faq)
                                                <div class="faq-item border rounded p-3 mb-3 bg-light">

                                                    <!-- FAQ ID -->
                                                    <input type="hidden" name="faq_id[]" value="{{ $faq->id }}">

                                                    <div class="form-group mb-3">
                                                        <label>Question</label>

                                                        <input type="text" name="faq_question[]" class="form-control"
                                                            value="{{ $faq->faq_question }}"
                                                            placeholder="Enter FAQ Question">
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label>Answer</label>

                                                        <textarea name="faq_answer[]" class="form-control" rows="4" placeholder="Enter FAQ Answer">{{ $faq->faq_answer }}</textarea>
                                                    </div>

                                                    <button type="button" class="btn btn-danger btn-sm removeFaq">

                                                        Remove
                                                    </button>

                                                </div>

                                            @empty

                                                <!-- Default Empty FAQ -->

                                                <div class="faq-item border rounded p-3 mb-3 bg-light">

                                                    <input type="hidden" name="faq_id[]" value="">

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

                                                </div>
                                            @endforelse

                                        </div>

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
                                        class="form-control bg-light @error('status') is-invalid @enderror"
                                        id="">
                                        <option value="active" {{ $page->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ $page->status == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
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

                                    @if (!empty($page->breadcrum_image))
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/image/page/' . $page->breadcrum_image) }}"
                                                alt="Breadcrumb Image" style="width: 120px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Header Option</label>
                                    <select name="header_option"
                                        class="form-control bg-light @error('header_option') is-invalid @enderror"
                                        id="">
                                        <option value="normal" {{ $page->header_option == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="transparent" {{ $page->header_option == 'transparent' ? 'selected' : '' }}>Transparent</option>
                                    </select>
                                    @error('header_option')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit"
                                        onclick="return confirm('Are you sure want to updated this?')">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if (($page->content_mode ?? 'normal') === 'custom')
        <div class="modal fade" id="pageAssetManagerModal" tabindex="-1" role="dialog" aria-labelledby="pageAssetManagerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="max-width: 1180px; width: 94%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="pageAssetManagerModalLabel"><i class="fas fa-images mr-2"></i>Page Asset Manager</h5>
                            <div class="small text-muted">Scans Custom HTML/CSS for local JPG, JPEG, PNG, WEBP and AVIF image references.</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('pages.preview', $page) }}" target="_blank" class="btn btn-outline-secondary btn-sm mr-2">Preview Draft</a>
                            <button type="button" class="btn btn-primary btn-sm mr-2" id="uploadAllAssetsBtn" disabled>
                                <i class="fas fa-cloud-upload-alt mr-1"></i> Upload All <span id="uploadAllAssetsCount"></span>
                            </button>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    </div>
                    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                                            @php
                                                $assetCount = $page->assets()->where('status', '!=', 'unused')->count();
                                                $uploadedCount = $page->assets()->where('status', 'uploaded')->count();
                                                $missingCount = $page->assets()->where('status', 'missing')->count();
                                                $activeAssets = $page->assets()->where('status', '!=', 'unused')->orderBy('section_name')->orderBy('id')->get();
                                                $unusedAssets = $page->assets()->where('status', 'unused')->get();
                                            @endphp
                                            <div class="d-flex flex-wrap align-items-center mb-3" style="gap:10px;">
                                                <form action="{{ route('pages.assets.scan', $page) }}" method="POST" class="mb-0">
                                                    @csrf
                                                    <button class="btn btn-info btn-sm" type="submit"><i class="fas fa-search"></i> Scan Saved HTML</button>
                                                </form>
                                                <span class="badge badge-secondary p-2">Found: {{ $assetCount }}</span>
                                                <span class="badge badge-success p-2">Uploaded: {{ $uploadedCount }}</span>
                                                <span class="badge badge-warning p-2">Missing: {{ $missingCount }}</span>
                                                <span class="small text-muted">Auto-scanned on Save/Update. Use this button only to re-scan the currently saved HTML/CSS.</span>
                                            </div>

                                            @if ($activeAssets->isEmpty())
                                                <div class="alert alert-light border mb-0">No local image slots were detected in the saved HTML/CSS. Add local JPG/JPEG/PNG/WebP/AVIF references and Update the page.</div>
                                            @else
                                                <div class="row">
                                                    @foreach ($activeAssets as $asset)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="border rounded p-3 h-100">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <div class="text-muted small">SECTION</div>
                                                                        <strong>{{ $asset->section_name ?: 'Unnamed Section' }}</strong>
                                                                    </div>
                                                                    @if ($asset->status === 'uploaded')
                                                                        <span class="badge badge-success">Uploaded</span>
                                                                    @else
                                                                        <span class="badge badge-warning">Missing</span>
                                                                    @endif
                                                                </div>
                                                                <hr>
                                                                <div class="small text-muted">EXPECTED FILE</div>
                                                                <code style="word-break:break-all;">{{ $asset->expected_filename }}</code>
                                                                @if ($asset->alt_text)
                                                                    <div class="small text-muted mt-2">ALT TEXT</div>
                                                                    <div>{{ $asset->alt_text }}</div>
                                                                @endif
                                                                <div class="small text-muted mt-2">REFERENCE</div>
                                                                <div class="small" style="word-break:break-all;">{{ $asset->reference }}</div>

                                                                @if ($asset->status === 'uploaded' && $asset->stored_path)
                                                                    <div class="mt-3">
                                                                        <img src="{{ asset('public/' . ltrim($asset->stored_path, '/')) }}" alt="" style="width:100%;height:180px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                                                    </div>
                                                                @endif

                                                                <form action="{{ route('pages.assets.upload', [$page, $asset]) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                                                    @csrf
                                                                    <div class="input-group input-group-sm">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input page-asset-file" name="image" accept=".jpg,.jpeg,.png,.webp,.avif" data-asset-id="{{ $asset->id }}" required>
                                                                            <label class="custom-file-label">Choose image</label>
                                                                        </div>
                                                                        <div class="input-group-append">
                                                                            <button class="btn btn-primary" type="submit">{{ $asset->status === 'uploaded' ? 'Replace' : 'Upload' }}</button>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted">Your local filename can be different; this upload is connected to the expected HTML slot automatically.</small>
                                                                </form>
                                                                @if ($asset->status === 'uploaded')
                                                                    <form action="{{ route('pages.assets.destroy', [$page, $asset]) }}" method="POST" class="mt-2" onsubmit="return confirm('Remove this uploaded image?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn-outline-danger btn-sm" type="submit">Remove Uploaded Image</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if ($unusedAssets->isNotEmpty())
                                                <div class="alert alert-secondary mt-3 mb-0">
                                                    <strong>{{ $unusedAssets->count() }} unused asset(s):</strong> these references were removed from the latest HTML scan. Uploaded files are retained for safety.
                                                </div>
                                            @endif
                    </div>
                    <form id="bulkAssetUploadForm" action="{{ route('pages.assets.bulk-upload', $page) }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        (function () {
            var modal = document.getElementById('pageAssetManagerModal');
            if (!modal) return;

            var uploadAllButton = document.getElementById('uploadAllAssetsBtn');
            var uploadAllCount = document.getElementById('uploadAllAssetsCount');
            var bulkForm = document.getElementById('bulkAssetUploadForm');
            var inputs = modal.querySelectorAll('.page-asset-file');

            function selectedInputs() {
                return Array.prototype.filter.call(inputs, function (input) {
                    return input.files && input.files.length > 0;
                });
            }

            function refreshBulkButton() {
                var selected = selectedInputs();
                uploadAllButton.disabled = selected.length === 0;
                uploadAllCount.textContent = selected.length ? '(' + selected.length + ')' : '';
            }

            Array.prototype.forEach.call(inputs, function (input) {
                input.addEventListener('change', function () {
                    var label = input.nextElementSibling;
                    if (label && label.classList.contains('custom-file-label')) {
                        label.textContent = input.files && input.files[0] ? input.files[0].name : 'Choose image';
                    }
                    refreshBulkButton();
                });
            });

            uploadAllButton.addEventListener('click', function () {
                var selected = selectedInputs();
                if (!selected.length) return;

                uploadAllButton.disabled = true;
                uploadAllButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Uploading...';

                var formData = new FormData();
                var token = bulkForm.querySelector('input[name="_token"]');
                if (token) formData.append('_token', token.value);

                selected.forEach(function (input) {
                    formData.append('images[' + input.getAttribute('data-asset-id') + ']', input.files[0]);
                });

                fetch(bulkForm.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/xhtml+xml'
                    }
                }).then(function (response) {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    if (!response.ok) throw new Error('Bulk upload failed.');
                    window.location.href = '{{ route('pages.edit', $page) }}?assets=1';
                }).catch(function () {
                    uploadAllButton.disabled = false;
                    uploadAllButton.innerHTML = '<i class="fas fa-cloud-upload-alt mr-1"></i> Upload All <span id="uploadAllAssetsCount">(' + selected.length + ')</span>';
                    alert('One or more images could not be uploaded. Please check the selected files and try again.');
                });
            });

            refreshBulkButton();
        })();
    </script>
@endsection


@section('js')
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>

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
                <div class="faq-item border rounded p-3 mb-3 bg-light">

                    <input type="hidden"
                        name="faq_id[]"
                        value="">

                    <div class="form-group mb-3">
                        <label>Question</label>

                        <input type="text"
                            name="faq_question[]"
                            class="form-control"
                            placeholder="Enter FAQ Question">
                    </div>

                    <div class="form-group mb-3">
                        <label>Answer</label>

                        <textarea name="faq_answer[]"
                            class="form-control"
                            rows="4"
                            placeholder="Enter FAQ Answer"></textarea>
                    </div>

                    <button type="button"
                        class="btn btn-danger btn-sm removeFaq">

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

    @if (($page->content_mode ?? 'normal') === 'custom' && request()->boolean('assets'))
        <script>
            $(document).ready(function () {
                $('#pageAssetManagerModal').modal('show');
            });
        </script>
    @endif
@stop
