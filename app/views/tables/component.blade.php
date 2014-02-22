<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
    @if(isset($r['limit']) && $r['limit']['over'] == true)
        <tr class="danger">
    @else
        <tr>
    @endif
        <td>
                <a href="{{$r['url']}}">{{$r['title']}}</a>
        </td>
        <td>
            {{mf($r['amount'],true)}}
        </td>
    </tr>
    @endforeach
</table>