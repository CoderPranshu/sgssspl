@extends('adminlte::page')

@section('title', 'Create Blogs')

@section('content_header')
    <h1>Create Blog</h1>
@stop

@section('content')
    @if (count($errors) > 0)

        <div class="alert alert-danger">

            <strong>Whoops!</strong> There were some problems with your input.<br><br>

            <ul>

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif
    <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="fas fa-robot text-primary"></i> Generate Blog with AI
                    </h5>
                </div>

                <div class="form-group">
                    <input type="text" id="ai_idea" class="form-control"
                        placeholder="Enter your blog idea... e.g. Winter hiking in Himalayas">
                </div>

                <button type="button" id="generateAI" class="btn btn-success">
                    <i class="fas fa-magic"></i> Generate Content
                </button>

                <div id="aiLoader" class="mt-2 text-primary" style="display:none;">
                    Generating content... Please wait...
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Title</label>
                                    <input type="text" placeholder="Enter title here...." class="form-control bg-light"
                                        name="title" value="{{ old('title') }}">
                                    @error('title')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Slug</label>
                                    <input type="text" placeholder="Enter slug here..." class="form-control bg-light"
                                        name="slug" value="{{ old('slug') }}">
                                    @error('slug')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>






                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea name="body" class="form-control bg-light summernote" placeholder="Enter description..." id="editor" cols="30"
                                        rows="10">{{ old('body') }}</textarea>
                                    @error('body')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Meta head</label>
                                    <textarea name="meta_head" class="form-control bg-light" placeholder="Enter description..." id="meta_editor"
                                        cols="30" rows="10"></textarea>
                                    @error('meta_head')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">excerpt</label>
                                    <textarea name="excerpt" class="form-control" placeholder="Enter excerpt..." id="" cols="10"
                                        rows="5"></textarea>
                                    @error('excerpt')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Tags</label>
                                    <select name="tags[]" class="form-control select2" multiple>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <select name="status" class="form-control bg-light" id="">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Categories</label>
                                    <select name="category_id" class="form-control bg-light" id="">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Featured Image</label>
                                    <input type="file" class="form-control bg-light" name="fetured_image">
                                    @error('featured_image')
                                        <b class="text-danger">{{ $message }}</b>
                                    @enderror

                                    <img id="imagePreview" src="" alt="Image Preview" class="img-fluid mt-2"
                                        style="display: none;">


                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100" type="submit">Submit</button>
                                </div>
                            </div>
                        </div>
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
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>

    <script>
        document.querySelector('input[name="featured_image"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>


    <script>
        // CKEDITOR.replace('editor');
        // CKEDITOR.replace('meta_editor');

        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Tags",
                allowClear: true
            });
        });





        $(document).ready(function() {

            $('#generateAI').click(function() {

                let idea = $('#ai_idea').val();

                if (!idea) {
                    alert('Please enter blog idea');
                    return;
                }

                $('#aiLoader').show();

                $.ajax({
                    url: "{{ route('blog.generate.ai') }}",
                    type: "POST",
                    data: {
                        idea: idea,
                        _token: "{{ csrf_token() }}"
                    },

                    success: function(response) {

                        $('input[name="title"]').val(response.title);
                        $('input[name="slug"]').val(response.slug);
                        $('textarea[name="body"]').val(response.body);
                        $('input[name="meta_title"]').val(response.meta_title);
                        $('input[name="meta_keywords"]').val(response.meta_keywords);
                        $('textarea[name="meta_description"]').val(response.meta_description);

                        $('#aiLoader').hide();
                    },

                    error: function(xhr, textStatus, errorThrown) {
                        console.log('AJAX Error:', errorThrown);
                        console.log('Status:', xhr.status);
                        console.log('Response:', xhr.responseText);

                        alert('Something went wrong. Check console.');
                        $('#aiLoader').hide();
                    }
                });

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
