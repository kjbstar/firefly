<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Firefly // Register</title>
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
          <h2>Register</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6 col-md-6">
          @if(isset($warning))
          <div class="alert alert-danger">
            <strong>Nope!</strong> {{$warning}}
          </div>
          @endif

          <p>
            To register, simply fill in your e-mail address. You will then receive an activation email message. Check your spam folder
            to be sure of its arrival.
          </p>

          {{Form::open(array('class' => 'form-inline'))}}
          <div class="form-group">
            <label class="sr-only" for="inputEmail">Email address</label>
            <input type="email" autocomplete="off" class="form-control" id="inputEmail" name="email" placeholder="Email">
          </div>
          <button type="submit" class="btn btn-primary">Register</button>
          {{Form::close()}}
          <ul style="margin-top:40px;">
            <li><a href="{{URL::route('login')}}">... or log in</a></li>
            <li><a href="{{URL::route('reset')}}">... or reset your password</a></li>
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>