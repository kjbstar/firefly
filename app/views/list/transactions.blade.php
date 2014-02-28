<table class="table table-striped table-bordered table-condensed">
            <tr>
                <th>Date</th>
                <th></th>
                <th>Description</th>
                <th>Amount</th>
                <th><small>Account</small></th>
                <th><small>Beneficiary</small></th>
                <th><small>Category</small></th>
                <th><small>Budget</small></th>
                <th>&nbsp;</th>
            </tr>
            @foreach($transactions as $t)
            <tr>
                <td><small>{{$t->date->format(Config::get('firefly.date_format'))
                    }}</small></td>
                <td>
                    @if($t->ignoreprediction == 1)
                    <span class="glyphicon glyphicon-eye-close" title="Ignored in predictions"></span>
                    @endif
                    @if($t->ignoreallowance == 1)
                    <span class="glyphicon glyphicon-gift" title="Ignored in
                    allowance"></span>
                    @endif
                    @if($t->mark == 1)
                    <span class="glyphicon glyphicon-ok" title="Marked in charts"></span>
                    @endif
                </td>
                <td><a href="{{URL::Route('edittransaction',
                [$t->id])}}">{{{$t->description}}}</a></td>
                <td>{{mf($t->amount,true,true)}}</td>
                <td><small><a href="{{URL::Route('accountoverview',
                [$t->account_id])}}">{{{$t->account()->first()
                        ->name}}}</a></small></td>
                <td><small>
                    @if(!is_null($t->beneficiary))
                    <a href="{{URL::Route('beneficiaryoverview',
                    [$t->beneficiary->id])}}">{{{$t->beneficiary->name}}}</a>
                    @endif
                    </small>
                </td>
                <td>
                    <small>
                    @if(!is_null($t->category))
                    <a href="{{URL::Route('categoryoverview',
                    [$t->category->id])}}">{{{$t->category->name}}}</a>
                    @endif
                    </small>
                </td>
                <td>
                    <small>
                    @if(!is_null($t->budget))
                    <a href="{{URL::Route('budgetoverview',
                    [$t->budget->id])}}">{{{$t->budget->name}}}</a>
                    @endif
                    </small>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{URL::Route('edittransaction',[$t->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="{{URL::Route('deletetransaction',[$t->id])}}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>