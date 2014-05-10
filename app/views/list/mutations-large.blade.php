<!-- LOOP ALL TRANSACTIONS OR TRANSFERS, DISPLAY A LARGE LIST -->
<table class="table table-bordered table-condensed">
    <tr>
        <th>&nbsp;</th>
        <th>Date</th>
        <th colspan="2">Description</th>
        <th>Amount</th>
        <th>Account(s)</th>
        <th>&nbsp;</th>
    </tr>
    @foreach($mutations as $m)
    <tr>
        <td>
            @foreach($types as $type)
                @if($m->hasComponentOfType($type))
                    <a href="{{URL::Route('componentoverview',$m->getComponentOfType($type)->id)}}">{{$m->getComponentOfType($type)->iconTag()}}</a>
                @endif
            @endforeach
        </td>
        <td>
            {{$m->date->format('M jS Y')}}
        </td>
        <td>
            <a href="{{URL::Route('edit'.strtolower(get_class($m)),$m->id)}}" title="Edit {{{$m->description}}}">
                {{{$m->description}}}
            </a>
        </td>
        <td>
            @if(!is_null($m->predictable_id))
                <a href="{{URL::Route('predictableoverview',$m->predictable_id)}}" title="{{{$m->predictable->description}}}"><span class="glyphicon glyphicon-refresh"></span></a>
            @endif
            @if($m->ignoreprediction)
            <span class="glyphicon glyphicon-eye-close"></span>
            @endif
            @if($m->ignoreallowance)
            <span class="glyphicon glyphicon-gift"></span>
            @endif
            @if($m->mark)
            <span class="glyphicon glyphicon-check"></span>
            @endif
        </td>
        <td>
            {{mf($m->amount,true)}}
        </td>
        <td>
            @if(get_class($m) == 'Transaction')
            <a href="{{URL::Route('accountoverview',$m->account_id)}}" title="Overview for {{{$m->account->name}}}">{{{$m->account->name}}}</a>
            @endif
            @if(get_class($m) == 'Transfer')
            <a href="{{URL::Route('accountoverview',$m->accountfrom_id)}}" title="Overview for {{{$m->accountfrom->name}}}">{{{$m->accountfrom->name}}}</a>
            &rarr;
            <a href="{{URL::Route('accountoverview',$m->accountto_id)}}" title="Overview for {{{$m->accountto->name}}}">{{{$m->accountto->name}}}</a>

            @endif
        </td>
        <td>
            <div class="btn-group btn-group-xs">
                <a href="{{URL::Route('edit'.strtolower(get_class($m)),$m->id)}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{URL::Route('delete'.strtolower(get_class($m)),$m->id)}}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
            </div>
        </td>
    </tr>
    @endforeach
</table>