<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">

        <!-- Favicon icon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}"> 
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <title>{{ $global->company_name }}</title>

        <style>
            :root {
                --main-color: {{ $theme->primary_color }};
            }

            {!! $theme->front_custom_css !!}
        </style>

        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900' type='text/css'>

        <!-- Stylesheet
        ========================= -->

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/login/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/login/vendor/font-awesome/css/all.min.css') }}">
        <link href="{{ asset('css/custom.css?v=2'.$build_version) }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/login/css/stylesheet.css') }}">
        
    </head>

    <div class="preloader" style="display: none;">
        <div class="lds-ellipsis" style="display: none;">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- Preloader End -->

    <div id="main-wrapper" class="oxyy-login-register"> 
        <div class="container-fluid px-0">
            <div class="row g-0 min-vh-100"> 
                <!-- Welcome Text
                ========================= -->
                <div class="col-md-6">
                    <div class="hero-wrap d-flex align-items-center h-100">
                        <div class="hero-mask opacity-8" style="background: var(--main-color);"></div>
                        <div class="hero-bg hero-bg-scroll" style="background-image: url(&#39;/assets/login/images/login-bg.jpg&#39;);"></div>
                        <div class="hero-content w-100 min-vh-100 d-flex flex-column">
                            <div class="row g-0">
                                <div class="col-11 col-sm-10 col-lg-9 mx-auto">
                                    <div class="logo mt-5 mb-5 mb-md-0"> <a class="d-flex" href="#" title="imedicalhs"><!--img src="./Multi-Service Portal_files/logo.png" class="img-fluid iRequest" alt="imedicalhs"--></a> </div>
                                </div>
                            </div>
                            <div class="row g-0 my-auto">
                                <div class="col-11 col-sm-10 col-lg-9 mx-auto">
                                    <h1 class="text-11 text-white mb-4">Welcome back!</h1>
                                    <p class="text-4 text-white lh-base mb-5">This is a secure proprietary application for the exclusive use of iMedical and its Affiliates. If you are not part of the organization identified above, please close this browser tab.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Welcome Text End --> 

                <!-- Login Form
                ========================= -->
                <div class="col-md-6 d-flex">
                    <div class="container my-auto py-5">
                        <div class="row g-0">
                            <div class="col-11 col-sm-10 col-lg-9 col-xl-8 mx-auto">
                              <!--h3 class="fw-600 mb-4 text-center"> <img class="img-logo" src="{{ asset('imedical_2024.png') }}" alt="logo"></h3-->
                                <div class="logo mb-md-0">
                                    <a class="d-flex" href="https://imedicalhs.com" title="imedicalhs" target="_blank">
                                        <img class="img-logo" src="{{ asset('assets/login/images/logo.png') }}" alt="logo">
                                    </a>
                                </div>
                                
                                @yield('content')
                                <p class="text-center text-muted mb-0 signup-p hide" style='display:none'>Don't have an account? <a id="to-register" class="link-primary" href="#">Sign Up</a></p>

                                <p class="text-center mt-3" style="font-size:10px">

                                    <a style="margin:40px;color:gray" href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a>

                                    <a style="margin:40px;color:gray" href="{{ route('terms-conditions') }}" target="_blank">Terms & Conditions</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Login Form End --> 
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/login/vendor/jquery/jquery.min.js') }}"></script> 
    <script src="{{ asset('assets/login/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> 
    <!-- Style Switcher --> 
    <script src="{{ asset('assets/login/js/switcher.min.js') }}"></script> 
    <script src="{{ asset('assets/login/js/theme.js') }}"></script>
    <script type="text/javascript">


$('#to-recover').on("click", function () {
    $("#loginform").hide();
    $("#registerform").hide();
    $("#recoverform").show();
});
$('#to-register').on("click", function () {
    $('.signup-p').hide();
    $("#registerform").show();
    //$('.leftLogin').hide();
    $("#loginform").hide();
    $("#recoverform").hide();
});
    </script>

</body>

</html>