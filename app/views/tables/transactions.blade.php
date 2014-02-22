<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
        <tr>
            <td>
                <small>{{strtolower($r->date->format('D d-M'))}}</small>
            </td>
        <td>
                <a href="{{URL::Route('edittransaction',$r->id)}}"
                    title="@foreach($r->components as $c) {{$c->type}}: {{$c->name}},@endforeach">{{$r->description}}</a>
        </td>
        <td>
            {{mf($r->amount,true)}}
        </td>
    </tr>
    @endforeach
</table>