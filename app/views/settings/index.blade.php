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
                           value="{{$predictionStart->value}}"
                           class="form-control"/>
                </p>

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Extended reporting</h3>
            </div>
            <div class="panel-body">
                <p>
                    Some pages can report extra on budgets,
                    categories or beneficiaries which have your interest.
                    Pick them.
                </p>
                <p>
                    {{Form::select('extendedReporting[]',$componentList,
                    $selectedComponents,
                    ['multiple' => true,'class' => 'form-control',
                    'size' => 20])}}
                </p>
            </div>

        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Frontpage account list</h3>
            </div>
            <div class="panel-body">
                <p>
                    The chart on the frontpage can contain any account (or
                    combination thereof) you wish.
                </p>
                {{Form::select('frontpageAccounts[]',$accountList,
                $selectedAccounts,
                ['class' => 'form-control','multiple' => 'multiple',
                'size' => 4])}}
                </div>
            </div>



            {{Form::submit('Submit',['class' => 'btn btn-info btn-lg'])}}
        {{Form::close()}}
    </div>

</div>
@endsection
@section('scripts')

@endsection