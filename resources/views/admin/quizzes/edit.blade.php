@extends('admin.layout.app')
@section('title')
    Dashboard | Admin
@endsection

@section('main-content')
    <div class="page-wrapper">
        <!-- Page-header start -->
        <div class="page-header">
            <div class="page-header-title">
                <h4> User List </h4>
                <span>There are all active and pending user list in here</span>
            </div>
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="{{url('/')}}">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <i class="icofont icofont-list"></i>
                        <a href="{{route('users.index')}}"> User List</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page-header end -->
        <!-- Page-body start -->
        <div class="page-body">
            <!-- Table header styling table start -->
            <div class="card">

                <div class="card-block ">
                    <div class="row justify-content-center">
                        <div class="col-md-6 ">
                            <div class="login-card card-block auth-body">
                                <form class="md-float-material" method="POST" action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data">
                                    @method('PATCH')
                                    @csrf
                                    <div class="auth-box">
                                        <div class="form-group">
                                            <label class="col-form-label">Name</label>
                                            <input type="text" name="name" value="{{old('name',$user->name)}}" required autocomplete="off" class="form-control" placeholder="Your name">
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                            <strong class="text-danger text-center">{{ $errors->first('name') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">Email</label>
                                            <input type="email" name="email" value="{{old('email',$user->email)}}" required autocomplete="off" class="form-control" placeholder="Your Email Address">
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                            <strong class="text-danger text-center">{{ $errors->first('email') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">Phone</label>
                                            <input type="text" name="phone" value="{{old('phone',$user->phone)}}" required autocomplete="off" class="form-control" placeholder="Your Phone Number">
                                            @if ($errors->has('phone'))
                                                <span class="help-block">
                                            <strong class="text-danger text-center">{{ $errors->first('phone') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">CV Link</label>
                                            <input type="text" name="cv_link" value="{{old('cv_link',$user->cv_link)}}" required autocomplete="off" class="form-control" placeholder="Your cv link">
                                            @if ($errors->has('cv_link'))
                                                <span class="help-block">
                                            <strong class="text-danger text-center">{{ $errors->first('cv_link') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">Status</label>
                                            <select name="status" id="userStatus" class="form-control">
                                                <option value="0" @if($user->status ==0 ) selected @endif>Rejected</option>
                                                <option value="1" @if($user->status ==1) selected @endif>Approved</option>
                                                <option value="2" @if($user->status==2) selected @endif>Pending</option>
                                            </select>

                                            @if ($errors->has('status'))
                                                <span class="help-block">
                                            <strong class="text-danger text-center">{{ $errors->first('status') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Update</button>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
                                </form>
                                <!-- end of form -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table header styling table end -->
        </div>
        <!-- Page-body end -->
    </div>
@endsection

@section('script')
    @endsection