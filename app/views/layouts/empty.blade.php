<!DOCTYPE html>
<html lang="en">
  <head>
      
    <title>Firefly Error</title>
      <base href="{{URL::asset('/')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/css/site.css" rel="stylesheet" media="screen">
      <meta charset="UTF-8" />
  </head>
  <body>
    <div class="container">
      @include('partials.flash')
      @yield('content')
        </div>
  </body>
</html>