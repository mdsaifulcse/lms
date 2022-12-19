<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from html.phoenixcoded.net/flatable/default/auth-normal-sign-in.html by HTTrack Website Copier/3.x [XR&CO'2010], Thu, 01 Sep 2022 08:17:57 GMT -->
<head>
    <title> @yield('title') </title>
    <!-- HTML5 Shim and Respond.js IE9 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Phoenixcoded">
    <meta name="keywords" content=", Flat ui, Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="Phoenixcoded">
    <!-- Favicon icon -->

    <link rel="icon" href="{{asset('admin/assets/images/favicon.ico')}}" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">

    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/icon/icofont/css/icofont.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/style.css')}}">
</head>

<body class="fix-menu">
<section class="login p-fixed d-flex text-center bg-primary common-img-bg">
    <!-- Container-fluid starts -->
    @yield('login-content')
    <!-- end of container-fluid -->
</section>
<!-- Warning Section Starts -->
<!-- Older IE warning message -->

<!-- Warning Section Ends -->
<!-- Required Jquery -->
<script type="text/javascript" src="{{asset('admin/assets/bower_components/jquery/dist/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/assets/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

</body>


<!-- Mirrored from html.phoenixcoded.net/flatable/default/auth-normal-sign-in.html by HTTrack Website Copier/3.x [XR&CO'2010], Thu, 01 Sep 2022 08:17:59 GMT -->
</html>
