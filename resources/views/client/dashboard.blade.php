@extends('admin.layout.app')
@section('title')
    Dashboard | Client
    @endsection

@section('main-content')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="page-header-title">
                <h4>This is a dashboard</h4>
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
                <div class="col-md-8 col-xl-3">
                    <div class="card social-widget-card">
                        <div class="card-block-big bg-facebook">
                            <h3>Dashboard</h3>
                            <span class="m-t-10">This is a dashboard</span>
                            <i class="icofont icofont-social-users"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection