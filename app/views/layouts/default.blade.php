<!DOCTYPE html>
<html lang="en">
  <head>
      
    <title>Firefly
    @if(isset($title))
    // {{{$title}}}
    @endif
    </title>
      <meta charset="UTF-8" />
      <base href="{{URL::asset('/')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/typeahead.js-bootstrap.css" rel="stylesheet" media="screen">
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script src="js/yepnope.1.5.4-min.js"></script>
    <script src="js/modernizr.custom.21599.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/site.js"></script>
      @yield('scripts')

  </body>
</html>