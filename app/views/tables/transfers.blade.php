<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
        <tr>
            <td style="width:80px;">
                <small>{{strtolower($r->date->format('D d-M'))}}</small>
            </td>
        <td>
                <a href="{{URL::Route('edittransaction',$r->id)}}"
                    title="{{$r->accountfrom->name}} &gt; {{$r->accountto->name}}">{{$r->description}}</a>
        </td>
            <td style="width:80px;">
            {{mf($r->amount,true)}}
        </td>
    </tr>
    @endforeach
</table>