<!-- LOOP ALL TRANSACTIONS, DISPLAY A MODEST LIST -->
<table class="table table-bordered table-condensed">
    @foreach($mutations as $m)
    <tr>
        <td>
            @foreach($types as $type)
            @if($m->hasComponentOfType($type))
                <a href="{{URL::Route('componentoverview',$m->getComponentOfType($type)->id)}}">{{$m->getComponentOfType($type)->iconTag()}}</a>
            @endif
            @endforeach
        </td>
        <td style="width: 90px;">
            <small>{{$m->date->format('M jS')}}</small>
        </td>
        <td>
            <a href="{{URL::Route('edit'.strtolower(get_class($m)),$m->id)}}" title="Edit {{{$m->description}}}">
                {{{$m->description}}}
            </a>
        </td>
        <td style="width:90px;">
            @if(get_class($m) == 'Transfer')
            <span title="{{{$m->accountfrom->name}}} &rarr; {{{$m->accountto->name}}}">{{mf($m->amount,true)}}</span>
            @else
            <span title="{{{$m->account->name}}}">{{mf($m->amount,true)}}</span>
            @endif

        </td>
    </tr>
    @endforeach
</table>