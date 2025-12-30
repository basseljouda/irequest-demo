<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="../plugins/images/favicon.png">
    <title>403 - Forbiddon Error</title>
    
    <link href="{{ asset("css/custom.css") }}"  rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" >
    <div class="error-box">
        <div class="error-body text-center" style="text-align:center;color:#555">
            <h1>403</h1>
            <h3 class="text-uppercase">Forbiddon Error!</h3>
            <p class="text-muted m-t-30 mb-4">YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE.</p>
            <a href="{{ url('/login') }}" class="btn btn-primary btn-rounded waves-effect waves-light m-b-40">Back to home</a> </div>
    </div>
</section>
</body>
</html>