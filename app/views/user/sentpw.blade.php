<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Firefly // Success!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link href="/css/site.css" rel="stylesheet" media="screen">
    @yield('styles')

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="/bootstrap/assets/js/html5shiv.js"></script>
      <script src="/bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h1>Firefly</h1>
          <h2>Password</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <p>
            Your new password is in the mail. <a href="{{URL::route('login')}}">Use it with your email address to log in</a>.
          </p>
        </div>
      </div>
    </div>
  </body>
</html>