<!DOCTYPE html>
<html lang="en">
  <head>
      
    <title>Firefly
    @if(isset($title))
    // {{$title}}
    @endif
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link href="/css/site.css" rel="stylesheet" media="screen">
    @yield('styles')
    
  </head>
  <body>
    <div class="container">
      @include('partials.menu')
      @include('partials.flash')
      @yield('content')
      <div style="height:80px;"></div>

      <div class="modal fade" id="PopupModal" tabindex="-1" role="dialog"  aria-hidden="true">

      </div><!-- /.modal -->

        <!-- Modal -->
        <div class="modal fade" id="LimitModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabeXl">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        Bla
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


    </div>
      <script src="/js/jquery-1.10.2.min.js"></script>
      <script src="/bootstrap/js/bootstrap.min.js"></script>

      @yield('scripts')

  </body>
</html>