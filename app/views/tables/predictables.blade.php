<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
    <tr>
        <td>
            <a href="{{URL::Route('predictableoverview',$r->id)}}">{{$r->description}}</a>
        </td>
        <td>
            {{mf($r->amount,true)}}
        </td>
    </tr>
    @endforeach
    <tr>
        <td>Sum</td>
        <td><strong>{{mf($sum,true)}}</strong></td>
    </tr>
</table>