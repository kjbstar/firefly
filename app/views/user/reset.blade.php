<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Firefly // Reset password</title>
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
          <h2>Reset password</h2>

        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          @if(isset($warning))
          <div class="alert alert-danger">
              <strong>Nope!</strong> {{$warning}}
          </div>
          @endif

          <p>
            To reset your password, simply enter your email address here.
          </p>

          {{Form::open(array('class' => 'form-inline'))}}
            <div class="form-group">
              <label class="sr-only" for="inputEmail">Email address</label>
              <input type="email" autocomplete="off" class="form-control" id="inputEmail" name="email" placeholder="Email">
            </div>
            <button type="submit" class="btn btn-primary">Reset me</button>
          {{Form::close()}}
          <p style="margin-top:40px;">
            <a href="/login" class="btn btn-info btn-xs">Login</a>
          </p>
        </div>
      </div>
    </div>
  </body>
</html>