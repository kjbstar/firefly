@if(Session::has('warning'))
<div class="alert alert-warning">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>Warning!</strong>
    @if(Session::has('warning_extended'))
        {{{Session::get('warning_extended')}}}
    @else
        {{{Session::get('warning')}}}
    @endif
</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>OK!</strong>
    @if(Session::has('success_extended'))
        {{{Session::get('success_extended')}}}
    @else
        {{{Session::get('success')}}}
    @endif
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<strong>Error!</strong>
    @if(Session::has('error_extended'))
        {{{Session::get('error_extended')}}}
    @else
        {{{Session::get('error')}}}
    @endif
</div>
@endif