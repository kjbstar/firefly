<table class="table table-striped table-bordered">
    <tr>
        <th>Date</th>
        <th>Description</th>
        <th>Amount</th>
        <th>From account</th>
        <th>To account</th>
        <th>&nbsp;</th>
    </tr>
    @foreach($transfers as $t)
    <tr>
        <td><small>{{$t->date->format(Config::get('firefly.date_format'))}}</small></td>
        <td><a href="{{URL::Route('edittransfer',
        [$t->id])}}">{{{$t->description}}}</a></td>
        <td>{{mf($t->amount,true)}}</td>
        <td><a href="{{URL::Route('accountoverview',[$t->accountfrom_id])
        }}">{{{$t->accountfrom()->first()->name}}}</a></td>
        <td><a href="{{URL::Route('accountoverview',
        [$t->accountto_id])}}">{{{$t->accountto()->first()->name}}}</a></td>
        <td>
            <div class="btn-group">
                <a href="{{URL::Route('edittransfer',[$t->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{URL::Route('deletetransfer',[$t->id])}}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
            </div>
        </td>
    </tr>
    @endforeach
</table>