<!DOCTYPE html>
<html lang="en">
  <head>
      
    <title>Firefly
    @if(isset($title))
    // {{{$title}}}
    @endif
    </title>
      <meta charset="UTF-8" />
      <base href="{{URL::to('/')}}/" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link href="css/site.css" rel="stylesheet" media="screen">
    @yield('styles')
    
  </head>
  <body>
    <div class="container">
      @include('partials.menu')
      @yield('breadcrumbs')
      @include('partials.flash')
      @yield('content')
      <div style="height:80px;"></div>

      <div class="modal fade" id="PopupModal" tabindex="-1" role="dialog"  aria-hidden="true"></div><!-- /.modal -->

    </div>
      <script src="js/jquery-1.10.2.min.js"></script>
      <script src="bootstrap/js/bootstrap.min.js"></script>
      @yield('scripts')

  </body>
</html>