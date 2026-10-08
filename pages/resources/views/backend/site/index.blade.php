@extends('adminlte::page')

@section('title', 'Sites')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1>Sites</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('site.create') }}" class="btn btn-primary float-right">Add Site</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Domain</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sites as $site)
                        <tr>
                            <td>{{ $site->id }}</td>
                            <td>{{ $site->user->name }}</td>
                            <td>{{ $site->domain }}</td>
                            <td>{{ $site->slug }}</td>
                            <td>{{ $site->status }}</td>
                            <td>
                                <a href="{{ route('site.edit', $site->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('site.destroy', $site->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')

@stop

@section('js')

@stop
