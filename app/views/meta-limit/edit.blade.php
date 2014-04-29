<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Edit {{$component->type->type}} limit for
            {{{$component->name}}} in month {{$limit->date->format('F Y')}}</h4>
      </div>
        {{Form::open(['class' => 'form-inline','role' => 'form'])}}
      <div class="modal-body">
        <p>
            By adding a limit to a {{$component->type->type}} you trigger an alert when it's amount is reached this month.
        </p>
        <div class="form-group">
            <label class="col-sm-3
            control-label" for="inputAmount">Amount</label>

            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{$limit->amount}}" step="any" name="amount"
                           class="form-control" id="inputAmount">
                </div>
            </div>
        </div>
        
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Set new limit</button>
      </div>
        {{Form::close()}}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->