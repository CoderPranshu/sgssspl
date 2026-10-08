@extends('adminlte::page')

@section('title', 'Pages')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pages</h1>
        <a href="{{ route('pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>Sr No.</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @forelse ($pages as $page)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $page->title }}</td>
                                <td>{{ $page->slug }}</td>
                                <td>
                                    @if ($page->status == 'active')
                                        <span class="badge badge-success">{{ $page->status }}</span>
                                    @elseif ($page->status == 'inactive')
                                        <span>{{ $page->status == 'inactive'}}</span>
                                    @endif
                                </td>
                                <td>

                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-pen"></i> Edit</a>
                                        <form action="{{ route('pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm ml-2">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr >
                                <td colspan="7" class="text-center">No Pages Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
