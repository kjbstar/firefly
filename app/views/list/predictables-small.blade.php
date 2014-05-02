<table class="table table-bordered">
    <?php $sum=0;?>
    @foreach($predictables as $predictable)
    <?php $sum += $predictable->amount;?>
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
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="text-align: right;"><em>Sum</em></td>
        <td colspan="2">~ {{mf($sum,true)}}</td>
    </tr>
</table>