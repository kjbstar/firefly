<!DOCTYPE html>
<html lang="en">
<head>
    <title>Firefly
        @if(isset($title))
        // {{{$title}}}
        @endif
    </title>
    <base href="{{URL::to('/')}}/" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/site.css" rel="stylesheet" media="screen">
    <!--[if lt IE 9]>
    <script src="bootstrap/assets/js/html5shiv.js"></script>
    <script src="bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Firefly</h1>
            @if(isset($title))
            <h2>{{{ucfirst($title)}}}</h2>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if(isset($warning))
            <div class="alert alert-danger">
                <strong>Nope!</strong> {{{$warning}}}
            </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>