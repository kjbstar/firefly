@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('settings'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h2>Settings</h2>

        <p>There are some settings for Firefly. It's not much yet,
            but enjoy!.</p>


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
                    <input type="date" name="predictionStart"
                           value="{{$predictionStart->format('Y-m-d')}}"
                           class="form-control"/>
                </p>

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Prediction style</h3>
            </div>
            <div class="panel-body">
                <p>
                    Firefly tries to predict your balance day-by-day. It looks at the same days, in the previous month.
                    For example: 10th of May, 10th of April, 10th of March. This is not perfect, but it works pretty
                    well.
                </p>
                <p>

                </p>
                <ol>
                    <li>Simply take the average of the difference between the day</li>
                </ol>

                <p>
                    <input type="date" name="predictionStart"
                           value="{{$predictionStart->format('Y-m-d')}}"
                           class="form-control"/>
                </p>

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Frontpage account</h3>
            </div>
            <div class="panel-body">
                <p>
                    The chart on the frontpage can contain any account you wish.
                </p>
                {{Form::select('frontpageAccount',$accountList,
                $frontpageAccount->id,
                ['class' => 'form-control',
                ])}}
                </div>
            </div>



            {{Form::submit('Submit',['class' => 'btn btn-info btn-lg'])}}
        {{Form::close()}}
    </div>

</div>
@endsection