@if(Session::has('warning'))
<div class="alert alert-warning">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>Warning!</strong> {{{Session::get('warning')}}}
</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>OK!</strong> {{{Session::get('success')}}}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>Error!</strong> {{{Session::get('error')}}}
</div>
@endif