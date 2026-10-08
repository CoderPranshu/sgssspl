@extends('adminlte::page')

@section('title', 'Edit Blog')

@section('content_header')
    <h1>Edit Blog</h1>
@stop

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <!-- LEFT -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <!-- TITLE -->
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" id="title" name="title" class="form-control"
                                value="{{ old('title', $blog->title) }}">
                        </div>

                        <!-- SLUG -->
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control"
                                value="{{ old('slug', $blog->slug) }}">
                        </div>

                        <!-- EXCERPT -->
                        <div class="form-group">
                            <label>Excerpt</label>
                            <textarea name="excerpt" class="form-control">{{ old('excerpt', $blog->excerpt) }}</textarea>
                        </div>

                        <!-- BODY -->
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="body" id="editor" class="form-control summernote">
{{ old('body', $blog->body) }}
</textarea>
                        </div>

                        <!-- META -->
                        <div class="form-group">
                            <label>Meta Head</label>
                            <textarea name="meta_head" id="meta_editor" placeholder="Enter meta head..." class="form-control" rows="10" cols="10">
{{ old('meta_head', $blog->meta_head) }}
</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">

                        <!-- TAGS -->
                        <div class="form-group">
                            <label>Tags</label>
                            <select name="tags[]" class="form-control select2" multiple>

                                {{-- @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ $blog->$tag->name ? 'selected' : '' }}>
                                        {{ $tag->name ?? '' }}
                                    </option>
                                @endforeach --}}

                                {{-- <option value="">{{ $blog->tags->id }}</option> --}}

                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ $blog->tags->contains($tag->id) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- STATUS -->
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $blog->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $blog->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <!-- CATEGORY -->
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control">

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $blog->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- IMAGE -->
                        <div class="form-group">
                            <label>Featured Image</label>
                            <input type="file" name="fetured_image" class="form-control">
                            <br>
                            @if ($blog->fetured_image)
                                <img src="{{ asset('uploads/images/featured_image/' . $blog->fetured_image) }}"
                                    class="img-fluid">
                            @endif
                        </div>
                        <button class="btn btn-primary w-100">Update Blog</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 45px !important;
            height: auto !important;
            padding: 5px;
        }

        .select2-container--default .select2-selection__choice {
            margin-top: 5px !important;
        }

        .select2-container--default .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
            width: auto !important;
            min-width: 120px;
            height: 28px;
            margin-top: 5px;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        let title = document.getElementById('title');
        let slug = document.getElementById('slug');

        title.addEventListener('keyup', function() {

            if (slug.dataset.manual === "true") return;

            slug.value = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
        });

        slug.addEventListener('input', function() {
            this.dataset.manual = "true";
        });

        // 🔥 editor
        // tinymce.init({
        //     selector: '#editor',
        //     height: 400
        // });
        // tinymce.init({
        //     selector: '#meta_editor',
        //     height: 300
        // });

        // 🔥 select2
        // $('.select2').select2();

        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Tags",
                allowClear: true
            });
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

@stop
