<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Stack admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, stack admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Đăng nhập PFMIS</title>
    <link rel="apple-touch-icon" href="<?=env('BASE_URL')?>/ij_task_logo_png.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?=env('BASE_URL')?>/ij_task_logo_png.png">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/vendors/css/forms/icheck/custom.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/css/app.css">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/app-assets/css/pages/login-register.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=env('BASE_URL')?>/assets/css/style.css">
    <!-- END Custom CSS-->
</head>
<body class="vertical-layout vertical-menu 1-column   menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 m-0">
                            <div class="card-header border-0" style="padding-bottom: 0px;">
                                <div class="card-title text-center">
                                    <div class="p-1"><img src="<?=env('BASE_URL')?>/ij_task_logo_png.png" alt="branding logo" style="height: 39px;"></div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Đăng nhập</span></h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form-horizontal form-simple" action="{{route('sysadmin.postLogin')}}" novalidate method="post">
                                        @csrf
                                        <fieldset class="form-group position-relative has-icon-left mb-0">
                                            <input type="text" class="form-control form-control-lg" id="user-name" placeholder="Tên đăng nhập" required name="username">
                                            <div class="form-control-position">
                                                <i class="ft-user"></i>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" class="form-control form-control-lg" id="user-password" placeholder="Mật khẩu" required  name="password">
                                            <div class="form-control-position">
                                                <i class="fa fa-key"></i>
                                            </div>
                                        </fieldset>
                                        {{--<div class="form-group row">--}}
                                            {{--<div class="col-md-6 col-12 text-center text-md-left">--}}
                                                {{--<fieldset>--}}
                                                    {{--<input type="checkbox" id="remember-me" class="chk-remember">--}}
                                                    {{--<label for="remember-me"> Remember Me</label>--}}
                                                {{--</fieldset>--}}
                                            {{--</div>--}}
                                            {{--<div class="col-md-6 col-12 text-center text-md-right"><a href="recover-password.html" class="card-link">Forgot Password?</a></div>--}}
                                        {{--</div>--}}
                                        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="ft-unlock"></i> Đăng nhập</button>
                                    </form>
                                </div>
                            </div>
                            {{--<div class="card-footer">--}}
                                {{--<div class="">--}}
                                    {{--<p class="float-sm-left text-center m-0"><a href="recover-password.html" class="card-link">Recover password</a></p>--}}
                                    {{--<p class="float-sm-right text-center m-0">New to Stack? <a href="register-simple.html" class="card-link">Sign Up</a></p>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->

<script src="<?=env('BASE_URL')?>/app-assets/vendors/js/vendors.min.js"></script>
<script src="<?=env('BASE_URL')?>/app-assets/vendors/js/forms/icheck/icheck.min.js"></script>
<script src="<?=env('BASE_URL')?>/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
<script src="<?=env('BASE_URL')?>/app-assets/js/core/app-menu.js"></script>
<script src="<?=env('BASE_URL')?>/app-assets/js/core/app.js"></script>
<script src="<?=env('BASE_URL')?>/app-assets/js/scripts/forms/form-login-register.js"></script>
<script>
    localStorage.setItem('login', 0);
</script>
</body>
</html>
