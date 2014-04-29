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
        <td>
            <small>{{$m->date->format('M jS')}}</small>
        </td>
        <td>
            <a href="{{URL::Route('edit'.strtolower(get_class($m)),$m->id)}}" title="Edit {{{$m->description}}}">
                {{{$m->description}}}
            </a>
        </td>
        <td>
            {{mf($m->amount,true)}}
        </td>
    </tr>
    @endforeach
</table>