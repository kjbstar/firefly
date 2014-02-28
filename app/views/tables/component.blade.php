<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
    @if(isset($r['limit']) && $r['limit']['over'] == true)
        <tr class="danger">
    @else
        <tr>
    @endif
        <td>
            @if(isset($r['parent']))
               <a href="{{$r['parent']['url']}}">{{$r['parent']['name']}}</a> /
            @endif
            <a href="{{$r['url']}}">{{$r['title']}}</a>
        </td>
        <td>
            {{mf($r['amount'],true)}}
        </td>
    </tr>
    @endforeach
</table>