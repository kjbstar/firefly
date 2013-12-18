<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reassign stuff
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="/css/site.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>
                        {{$count}} to go, {{$total}} in total.
                    </p>

                    @foreach($data as $t)
                    <table class="table table-bordered">
                        <tr>
                            <td style="width:30%">Transaction #{{$t->id}}<br /><strong>{{$t->description}}</strong></td>
                            <td style="width:70%"><strong>{{$t->description}}</strong> / {{$t->date->format('l d F Y')}} / {{mf($t->amount,true)}}<br />{{$t->tags}}</td>
                        </tr>
                        <tr>
                            @if(is_null($t->beneficiary_id))
                            <td><em>No beneficiary!</em></td>
                            <td>
                                {{Form::open(['class' => 'form-inline'])}}
                                {{Form::hidden('type','beneficiary')}}
                                {{Form::hidden('transaction',$t->id)}}
                                <div class="form-group">
                                    {{Form::select('beneficiary_id',$beneficiaries,$t->defaultBeneficiary,['class'=>'form-control','style' => 'width:250px;'])}}
                                </div>
                                <div class="form-group">
                                    {{Form::submit('Save',['class' => 'form-control btn btn-default'])}}
                                </div>
                                {{Form::close()}}
                            </td>
                            @else
                            <td>Beneficiary:</td>
                            <td>{{$t->beneficiary()->first()->name}}</td>
                            @endif
                        </tr>
                        <tr>
                            @if(is_null($t->category_id))
                            <td><em>No category!</em></td>
                            <td>
                                {{Form::open(['class' => 'form-inline'])}}
                                {{Form::hidden('type','category')}}
                                {{Form::hidden('transaction',$t->id)}}
                                <div class="form-group">
                                    {{Form::select('category_id',$categories,$t->defaultCategory,['class'=>'form-control','style' => 'width:250px;'])}}
                                </div>
                                <div class="form-group">
                                    {{Form::submit('Save',['class' => 'form-control btn btn-default'])}}
                                </div>
                                {{Form::close()}}

                            </td>
                            @else
                            <td>Category:</td>
                            <td>{{$t->category()->first()->name}}</td>
                            @endif
                        </tr>
                        <tr>
                            @if(is_null($t->budget_id))
                            <td><em>No budget!</em></td>
                            <td>
                                {{Form::open(['class' => 'form-inline'])}}
                                {{Form::hidden('type','budget')}}
                                {{Form::hidden('transaction',$t->id)}}
                                <div class="form-group">
                                    {{Form::select('budget_id',$budgets,$t->defaultBudget,['class'=>'form-control','style' => 'width:250px;'])}}
                                </div>
                                <div class="form-group">
                                    {{Form::submit('Save',['class' => 'form-control btn btn-default'])}}
                                </div>
                                {{Form::close()}}
                            </td>
                            @else
                            <td>Budget:</td>
                            <td>{{$t->budget()->first()->name}}</td>
                            @endif
                        </tr>
                        <tr>

                            <td>
                                {{Form::open(['class' => 'form-inline'])}}
                                {{Form::hidden('type','ignore')}}
                                {{Form::hidden('transaction',$t->id)}}
                                <div class="form-group">
                                    {{Form::submit('Ignore',['class' => 'form-control btn btn-default btn-warning'])}}
                                </div>
                                {{Form::close()}}
                            </td>
                            <td>
                                {{Form::open(['class' => 'form-inline'])}}
                                {{Form::hidden('type','save_all')}}
                                {{Form::hidden('transaction',$t->id)}}
                                {{Form::hidden('beneficiary_id',$t->defaultBeneficiary)}}
                                {{Form::hidden('category_id',$t->defaultCategory)}}
                                {{Form::hidden('budget_id',$t->defaultBudget)}}
                                <div class="form-group">
                                    {{Form::submit('Save these defaults',['class' => 'form-control btn btn-default btn-info'])}}
                                </div>
                                {{Form::close()}}
                            </td>
                        </tr>



                    </table>
                    @endforeach

                </div>
            </div>

            <div style="height:80px;"></div>


            <script src="/js/jquery-2.0.3.min.js"></script>
            <script src="/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>