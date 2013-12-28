<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Overview</h4>
        </div>
        <div class="modal-body">


<table class="table table-striped table-bordered table-condensed">
    <tr>
        <th>Date</th>
        <th>Description</th>
        <th>Amount</th>
        <th>%</th>
        <th>&nbsp;</th>
    </tr>
    @foreach($transactions as $t)
    <tr>
        <td>{{$t->date->format('d-m-Y')}}</td>
        <td><a href="{{URL::Route('edittransaction',
        [$t->id])}}">{{{$t->description}}}</a>
        </td>
        <td>{{mf($t->amount,true)}}</td>
        <td>{{$t->pct}}%</td>
        <td>
            <div class="btn-group">
                <a href="{{URL::Route('edittransaction',[$t->id])}}"
                   class="btn btn-default btn-xs"><span
                        class="glyphicon glyphicon-pencil"></span></a>
                <a href="{{URL::Route('deletetransaction',[$t->id])}}"
                   class="btn btn-danger btn-xs"><span
                        class="glyphicon glyphicon-trash"></span></a>
            </div>
        </td>
    </tr>
    @endforeach
    <tr>
        <td colspan="3">Sum:</td>
        <td><em>{{mf($sum)}}</em></td>
    </tr>
</table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->