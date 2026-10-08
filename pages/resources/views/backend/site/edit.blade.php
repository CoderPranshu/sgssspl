@extends('adminlte::page')

@section('title', 'Edit Site')

@section('content_header')
    <h1>Edit Site</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('site.update', $site->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="domain">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain" placeholder="Enter domain" value="{{ $site->domain }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter slug" value="{{ $site->slug }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" {{ $site->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $site->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Update Site</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        document.getElementById('domain').addEventListener('input', function() {
            const domain = this.value;
            const slug = domain.toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            document.getElementById('slug').value = slug;
        });
    </script>
@stop
