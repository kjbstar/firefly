@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('settings'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h2>Settings</h2>

        <p>There are some settings for Firefly. It's not much yet, but enjoy!.</p>


    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        {{Form::open()}}

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Prediction start</h3>
            </div>
            <div class="panel-body">
                <p>
                    Transactions that occured before this date are ignored in
                    the balance prediction routine.
                </p>

                <p>
                    <input type="date" name="predictionStart" value="{{$predictionStart->format('Y-m-d')}}" class="form-control"/>
                </p>

            </div>
        </div>

        @if(!is_null($frontpageAccount))
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Frontpage account</h3>
            </div>
            <div class="panel-body">
                <p>
                    The frontpage used to be capable of showing just one account. It has a small drop down
                    now, but still defaults to this account:
                </p>
                {{Form::select('frontpageAccount',$accountList,$frontpageAccount->id,['class' => 'form-control',])}}
                </div>
            </div>
        @endif

            {{Form::submit('Submit',['class' => 'btn btn-info'])}}
        {{Form::close()}}
    </div>

</div>
@endsection