@extends('adminlte::page')

@section('title', 'Tags')

@section('content_header')
    <h1>Tag Management</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="row">

    <!-- ADD TAG -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Add Tag
            </div>
            <div class="card-body">

                <form action="{{ route('tag.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Tag Name</label>
                        <input type="text" name="name" id="tag_name" class="form-control" placeholder="Enter tag">
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="tag_slug" class="form-control" placeholder="Slug">
                    </div>

                    <button class="btn btn-success w-100">Add Tag</button>
                </form>

            </div>
        </div>
    </div>

    <!-- TAG LIST -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                All Tags
            </div>

            <div class="card-body">

                <table class="table table-striped">
                    <tr>
                        <th>Sr no</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Action</th>
                    </tr>

                    @foreach($tags as $tag)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->slug }}</td>

                        <td>
                            <!-- EDIT -->
                            <button class="btn btn-primary btn-sm editBtn"
                                data-id="{{ $tag->id }}"
                                data-name="{{ $tag->name }}"
                                data-slug="{{ $tag->slug }}">
                                Edit
                            </button>

                            <!-- DELETE -->
                            <form action="{{ route('tag.destroy',$tag->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                </table>

            </div>
        </div>
    </div>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5>Edit Tag</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="edit_slug" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

@stop


@section('js')

<script>
/* 🔥 Real-time slug generate (Add form) */
document.getElementById('tag_name').addEventListener('keyup', function(){

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g,'')
        .replace(/\s+/g,'-')
        .replace(/--+/g,'-');

    document.getElementById('tag_slug').value = slug;
});


/* 🔥 Edit modal */
$('.editBtn').click(function(){

    let id = $(this).data('id');
    let name = $(this).data('name');
    let slug = $(this).data('slug');

    $('#edit_name').val(name);
    $('#edit_slug').val(slug);

    $('#editForm').attr('action','/tag/'+id);

    $('#editModal').modal('show');
});


/* 🔥 Real-time slug in edit */
$('#edit_name').on('keyup', function(){

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g,'')
        .replace(/\s+/g,'-')
        .replace(/--+/g,'-');

    $('#edit_slug').val(slug);
});

</script>

@stop
