    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabeXl">Prediction for {{$date->format('j F Y')}}</h4>
            </div>
            <div class="modal-body">
<table class="table">
    <tr>
        <th>Prediction</th>
        <th>Optimistic</th>
        <th>Alt1</th>
        <th>Alt2</th>
        <th>Pessimistic</th>

    </tr>
    <tr>
        <td><strong>{{mf($prediction['prediction']['prediction']*-1,true)}}</strong></td>
        <td>{{mf($prediction['prediction']['most']*-1,true)}}</td>
        <td>{{mf($prediction['prediction']['prediction_alt1']*-1,true)}}</td>
        <td>{{mf($prediction['prediction']['prediction_alt2']*-1,true)}}</td>
        <td>{{mf($prediction['prediction']['least']*-1,true)}}</td>
    </tr>
    <tr>
        <td><strong>{{mf($input['balance'],true)}}</strong></td>
        <td>{{mf($input['optimistic'],true)}}</td>
        <td>{{mf($input['alt1'],true)}}</td>
        <td>{{mf($input['alt2'],true)}}</td>
        <td>{{mf($input['pessimistic'],true)}}</td>

    </tr>
</table>

<h5>Based on these transactions</h5>

<table class="table table-condensed">
    <tr>
        <th>&nbsp;</th>
        <th>Date</th>
        <th>Description</th>
        <th>Amount</th>
    </tr>
    @foreach($prediction['transactions'] as $date => $list)
    @foreach($list as $t)
    <tr>
        <td><span class="glyphicon glyphicon-euro"</td>
        <td>{{$t->date->format('d-m-Y')}}</td>
        <td>{{{$t->description}}}</td>
        <td>{{mf($t->amount,true)}}</td>
    </tr>
    @endforeach
    @endforeach

    @foreach($prediction['predictables'] as  $p)
    <tr>
        <td><span class="glyphicon glyphicon-refresh"</td>
        <td>{{$p->date->format('jS')}}</td>
        <td>{{{$p->description}}}</td>
        <td>{{mf($p->amount,true)}}</td>
    </tr>
    @endforeach


</table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
