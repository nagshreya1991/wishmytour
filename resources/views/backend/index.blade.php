<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Wishmytour Admin</title>
    @include('backend.layouts.head')
</head>
<body>
<!--Preloader-->
<div class="preloader-it">
    <div class="la-anim-1"></div>
</div>
<!--/Preloader-->

<div class="wrapper pa-0">
    <header class="sp-header">
        <div class="sp-logo-wrap pull-left">
            <a href="{{ url('admin') }}">
                <img class="brand-img mr-10" src="{{asset('public/backend/dist/img/logo.png')}}" alt="brand"/>
                <span class="brand-text">Wishmytour</span>
            </a>
        </div>

        <div class="clearfix"></div>
    </header>

    <!-- Main Content -->
    <div class="page-wrapper pa-0 ma-0 auth-page">
        <div class="container-fluid">
            <!-- Row -->
            <div class="table-struct full-width full-height">
                <div class="table-cell vertical-align-middle auth-form-wrap">
                    <div class="auth-form  ml-auto mr-auto no-float">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="mb-30">
                                    <h3 class="text-center txt-dark mb-10">Sign in to Wishmytour Admin</h3>
                                    <h6 class="text-center nonecase-font txt-grey">Enter your details below</h6>
                                </div>
                                <div class="form-wrap">
                                    <form action="{{ url('admin') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="control-label mb-10" for="exampleInputEmail_2">Email
                                                address</label>
                                            <input type="email" class="form-control" required=""
                                                   id="exampleInputEmail_2" placeholder="Enter email" name="email">
                                        </div>
                                        <div class="form-group">
                                            <label class="pull-left control-label mb-10" for="exampleInputpwd_2">Password</label>
{{--                                            <a class="capitalize-font txt-primary block mb-10 pull-right font-12"--}}
{{--                                               href="forgot-password.html">forgot password ?</a>--}}
{{--                                            <div class="clearfix"></div>--}}
                                            <input type="password" class="form-control" required=""
                                                   id="exampleInputpwd_2" placeholder="Enter pwd" name="password">
                                        </div>

{{--                                        <div class="form-group">--}}
{{--                                            <div class="checkbox checkbox-primary pr-10 pull-left">--}}
{{--                                                <input id="checkbox_2" required="" type="checkbox">--}}
{{--                                                <label for="checkbox_2"> Keep me logged in</label>--}}
{{--                                            </div>--}}
{{--                                            <div class="clearfix"></div>--}}
{{--                                        </div>--}}
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-info btn-success btn-rounded">sign in
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Row -->
        </div>

    </div>
    <!-- /Main Content -->

</div>
<!-- jQuery -->
<script src="{{asset('public/backend/vendors/bower_components/jquery/dist/jquery.min.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{asset('public/backend/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

<!-- Init JavaScript -->
<script src="{{asset('public/backend/dist/js/init.js')}}"></script>


</body>

</html>