<table class="table table-bordered">
    @foreach($predictables as $predictable)
    <tr>
        <td>
            @foreach($types as $type)
            @if($predictable->hasComponentOfType($type))
                {{$predictable->getComponentOfType($type)->iconTag()}}
            @endif
            @endforeach
        </td>
        <td>
            {{$predictable->dayOfMonth()}}
        </td>
        <td>
            <a href="{{URL::Route('predictableoverview',$predictable->id)}}" title="Overview for {{{$predictable->description}}}">
            {{{$predictable->description}}}
            </a>
        </td>
        <td>
            ~ {{mf($predictable->amount,true)}}
        </td>
        <td>
            <a class="btn btn-default btn-xs" href="{{URL::Route('addtransaction',$predictable->id)}}"><span class="glyphicon glyphicon-plus-sign"></span></a>
        </td>

    </tr>
    @endforeach
</table>