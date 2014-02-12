@extends('layouts.user')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <p>
            Your password has been reset. Check your email. <a
                href="{{URL::route
            ('login')
            }}">Use it with your email address to log in</a>.
        </p>
    </div>
</div>
@stop