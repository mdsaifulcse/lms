@extends('admin.layout.app')
@section('title')
    Dashboard | Admin
    @endsection

@section('main-content')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="page-header-title">
                <h4>Dashboard</h4>
            </div>
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="index-2.html">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Pages</a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="page-body">
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card">
                        <div class="card-block-big bg-facebook">
                            <h3>This is a dashboard</h3>
                            <span class="m-t-10">This is a dashboard</span>
                            <i class="icofont icofont-social-facebook"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card">
                        <div class="card-block-big bg-twitter">
                            <h3>780 +</h3>
                            <span class="m-t-10">Twitter Users</span>
                            <i class="icofont icofont-social-twitter"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card">
                        <div class="card-block-big bg-linkein">
                            <h3>998 +</h3>
                            <span class="m-t-10">Linked In Users</span>
                            <i class="icofont icofont-brand-linkedin"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card">
                        <div class="card-block-big bg-google-plus">
                            <h3>650 +</h3>
                            <span class="m-t-10">Google Plus Users</span>
                            <i class="icofont icofont-social-google-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection