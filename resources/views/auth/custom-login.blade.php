@extends('layouts.login-app')

@section('title')
    Login to your account
    @endsection

@section('login-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <!-- Authentication card start -->
                <div class="login-card card-block auth-body">
                    <form class="md-float-material"  method="POST" action="{{ url('/') }}">
                        @csrf
                        <div class="text-center">
                            <h1>Login your account</h1>
                            {{--<img src="{{'admin/assets/images/auth/logo.png'}}" alt="logo.png">--}}
                        </div>
                        <div class="auth-box">
                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    <h3 class="text-center txt-primary">Sign In</h3>
                                </div>
                            </div>
                            <hr/>
                            <div class="input-group">
                                <input type="text" name="auth" class="form-control" placeholder="Your Username" required>
                                <span class="md-line"></span>
                            </div>

                            <div class="input-group">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                <span class="md-line"></span>
                            </div>
                            <div>
                                @if ($errors->has('auth'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('auth') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="row m-t-25 text-left">
                                {{--<div class="col-sm-7 col-xs-12">--}}
                                    {{--<div class="checkbox-fade fade-in-primary">--}}
                                        {{--<label>--}}
                                            {{--<input type="checkbox" value="">--}}
                                            {{--<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>--}}
                                            {{--<span class="text-inverse">Remember me</span>--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            </div>
                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Sign in</button>
                                </div>
                            </div>
                            <hr/>
                            <div class="col-sm-12 col-xs-12 forgot-phone text-right">
                                <a href="{{route('register')}}" class="text-right f-w-600 text-inverse">Not Register? > Register</a>
                            </div>
                            {{--<div class="row">--}}
                            {{--<div class="col-md-10">--}}
                            {{--<p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p>--}}
                            {{--<p class="text-inverse text-left"><b>Your Autentification Team</b></p>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2">--}}
                            {{--<img src="{{'admin/assets/images/auth/Logo-small-bottom.png'}}" alt="small-logo.png">--}}
                            {{--</div>--}}
                            {{--</div>--}}

                        </div>
                    </form>
                    <!-- end of form -->
                </div>
                <!-- Authentication card end -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
    </div>
    @endsection