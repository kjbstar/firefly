<table class="table table-bordered">
    <tr>
        <th colspan="2">Description</th>
        <th colspan="2">Amount</th>
        <th>Day of month</th>
        <th>&nbsp;</th>
    </tr>
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
            <a href="{{URL::Route('predictableoverview',$predictable->id)}}" title="Overview for {{{$predictable->description}}}">
            {{{$predictable->description}}}
            </a>
        </td>
        <td>
            &gt; {{mf($predictable->maximumAmount(),true)}}
        </td>
        <td>
            &lt; {{mf($predictable->minimumAmount(),true)}}
        </td>
        <td>
            {{$predictable->dayOfMonth()}}
        </td>
        <td>
            <div class="btn-group">
                <!-- edit -->
                <a class="btn btn-default btn-sm" href="{{URL::Route('editpredictable',$predictable->id)}}" title="Edit {{{$predictable->description}}}"><span class="glyphicon glyphicon-pencil"></span></a>
                <!-- delete -->
                <a class="btn btn-danger btn-sm" href="{{URL::Route('deletepredictable',$predictable->id)}}" title="Delete {{{$predictable->description}}}"><span class="glyphicon glyphicon-trash"></span></a>
            </div>
        </td>
    </tr>
    @endforeach
</table>