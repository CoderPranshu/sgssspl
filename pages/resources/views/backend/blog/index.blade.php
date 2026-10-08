@extends('adminlte::page')

@section('title', 'Blogs')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Blogs</h1>
        <a href="{{ route('blog.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped hoverable">
                        <thead>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Featured Image</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($blogs as $blog)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $blog->title }}</td>
                                    <td>{{ $blog->slug }}</td>
                                    <td>{{ $blog->status }}</td>
                                    <td>
                                        <img style="width: 100px;" src="{{ asset('uploads/images/featured_image/'.$blog->fetured_image) }}" alt="">
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-primary"><i class="fas fa-pen"></i></a>

                                            <form action="{{ route('blog.destroy', $blog->id) }}" method="post">
												 @csrf
                                                 @method('DELETE')
                                                <button type="submit" class="btn btn-danger ml-2" onclick="return alert('Are you sure want to delete this?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
