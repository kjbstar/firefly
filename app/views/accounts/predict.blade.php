    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabeXl">Prediction for {{$date->format('j F Y')}}</h4>
            </div>
            <div class="modal-body">
<table class="table">
    <tr>
        <th>Optimistic</th>
        <th>Prediction</th>
        <th>Pessimistic</th>

    </tr>
    <tr>
        <td>{{mf($prediction['least'],true)}}</td>
        <td>{{mf($prediction['prediction'],true)}}</td>
        <td>{{mf($prediction['most'],true)}}</td>
    </tr>
</table>

<h5>Based on this information</h5>
<table class="table table-bordered">
    <tr>
        <th>Date</th>
        <th>Total amount</th>
        <th>Average amount</th>
    </tr>
    @foreach($information as $row)
    <tr>
        <td>{{$row->day->format('jS F')}}</td>
        <td>{{mf($row->sum_of_day)}}</td>
        <td>{{mf($row->average_of_day)}}</td>
    </tr>
    @endforeach
</table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
