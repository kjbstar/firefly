<table class="table table-condensed table-bordered">

    <tr>
        <th>Date</th>
        <th>Predicted</th>
        <th>Actual</th>
    </tr>
    @foreach($rows as $r)
        @if($r['date'] < $today)
        <tr>
            <td><small>{{strtolower($r['date']->format('D d-M'))}}</small></td>
            <td><small>{{mf($r['prediction'],true)}}</small></td>
            <td><small>{{mf($r['actual'],true)}}</small></td>
        </tr>
    @else
        <tr>
            <td>{{strtolower($r['date']->format('D d-M'))}}</td>
            <td>{{mf($r['prediction'],true)}}</td>
            <td>&dash;</td>
        </tr>
    @endif
    @endforeach
</table>