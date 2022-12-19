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
                    <form class="md-float-material" method="POST" action="{{ url('/') }}">
                        @csrf
                        <div class="text-center">
                            <img src="{{asset('admin/assets/images/auth/logo.png')}}" alt="logo.png">
                        </div>
                        <div class="auth-box">
                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    <h3 class="text-center txt-primary">Sign up. It is fast and easy.</h3>
                                </div>
                            </div>
                            <hr/>
                            <div class="form-group">
                                <input type="text" name="name" value="{{old('name')}}" required autocomplete="off" class="form-control" placeholder="Your name">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('name') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" value="{{old('email')}}" required autocomplete="off" class="form-control" placeholder="Your Email Address">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('email') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone" value="{{old('phone')}}" required autocomplete="off" class="form-control" placeholder="Your Phone Number">
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('phone') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="text" name="cv_link" value="{{old('cv_link')}}" required autocomplete="off" class="form-control" placeholder="Your cv link">
                                @if ($errors->has('cv_link'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('cv_link') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" required autocomplete="off" class="form-control" placeholder="Choose Password">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('password') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="password" name="password_confirmation" required autocomplete="off" class="form-control" placeholder="Confirm Password">
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                            <strong class="text-warning text-center">{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                @endif
                            </div>
                            {{--<div class="row m-t-25 text-left">--}}
                                {{--<div class="col-md-12">--}}
                                    {{--<div class="checkbox-fade fade-in-primary">--}}
                                        {{--<label>--}}
                                            {{--<input type="checkbox" value="">--}}
                                            {{--<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>--}}
                                            {{--<span class="text-inverse">I read and accept <a href="#">Terms &amp; Conditions.</a></span>--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-12">--}}
                                    {{--<div class="checkbox-fade fade-in-primary">--}}
                                        {{--<label>--}}
                                            {{--<input type="checkbox" value="">--}}
                                            {{--<span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>--}}
                                            {{--<span class="text-inverse">Send me the <a href="#">Newsletter</a> weekly.</span>--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Sign up now.</button>
                                </div>
                            </div>
                            <hr/>
                            <div class="col-sm-12 col-xs-12 forgot-phone text-right">
                                <a href="{{route('login')}}" class="text-right f-w-600 text-inverse">Already Register? > Login</a>
                            </div>
                            {{--<div class="row">--}}
                                {{--<div class="col-md-10">--}}
                                    {{--<p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p>--}}
                                    {{--<p class="text-inverse text-left"><b>Your Autentification Team</b></p>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-2">--}}
                                    {{--<img src="assets/images/auth/Logo-small-bottom.png" alt="small-logo.png">--}}
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