@extends('adminlte::page')

@section('title', 'Sites')

@section('content_header')
    <h1>Site setting</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif



    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">

                    <div class="text-center">
                        <i class="fas fa-user-circle img-fluid img-circle" style="font-size: 100px; color: #6c757d;"></i>
                    </div>

                    <h3 class="profile-username text-center">
                        {{ $admin->name }}
                    </h3>

                    <p class="text-muted text-center">
                        {{ $admin->email }}
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">

                        <li class="list-group-item">
                            <b>Total Logins</b>
                            <a class="float-right">{{ $admin->login_count }}</a>
                        </li>

                        <li class="list-group-item">
                            <b>Last Login</b>
                            <a class="float-right">
                                {{ $admin->last_login_at ? $admin->last_login_at->format('d M Y, h:i A') : 'N/A' }}
                            </a>
                        </li>

                        <li class="list-group-item">
                            <b>Last Login IP</b>
                            <a class="float-right">{{ $admin->last_login_ip ?? 'N/A' }}</a>
                        </li>

                    </ul>

                    <a href="#" class="btn btn-primary btn-block">
                        <b>Edit Profile</b>
                    </a>

                </div>
            </div>
            <!-- /.card -->

        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Profile </a></li>
                        <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Site Settings</a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="timeline">
                            <form class="form-horizontal" method="POST" action="{{ route('profile.update') }}">
                                @csrf()
                                <div class="form-group row">
                                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name" class="form-control" id="inputName"
                                            placeholder="Name" value="{{ auth()->user()->name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" name="email" class="form-control" id="inputEmail"
                                            placeholder="Email" value="{{ auth()->user()->email }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div>
                            </form>

                            <form class="form-horizontal mt-4" method="POST"
                                action="{{ route('profile.password.update') }}">
                                @csrf()

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Current Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="current_password" class="form-control"
                                            placeholder="Enter current password">
                                        @error('current_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">New Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Enter new password">
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Confirm Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Confirm password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane active" id="settings">


                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Header Path</label>
                                                    <input type="text" name="header_path" class="form-control"
                                                        value="{{ $setting->header_path }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Footer Path</label>
                                                    <input type="text" name="footer_path" class="form-control"
                                                        value="{{ $setting->footer_path }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Head(Full meta details)</label>
                                                    <textarea name="head" id="" cols="30" rows="5" class="form-control">{{ $setting->head }}</textarea>
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Css Links</label>
                                                    <textarea name="css" id="" cols="30" rows="5" class="form-control">{{ $setting->css }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Js Links & Custom js</label>
                                                    <textarea name="js" id="" cols="30" rows="5" class="form-control">{{ $setting->js }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <button class="btn btn-primary">Update Settings</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>



@stop

@section('css')

@stop

@section('js')

@stop
