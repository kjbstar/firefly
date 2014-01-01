<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Set a new budget</h4>
      </div>
        {{Form::open(['class' => 'form-horizontal','role' => 'form'])}}
      <div class="modal-body">
        <p>
        Set a specific allowance for a month of your choice.
        </p>
        <div class="form-group">
            <label class="col-sm-3
            control-label" for="inputAmount">Amount</label>
            <div class="col-sm-9">
            <div class="input-group">
                <span class="input-group-addon">&euro;</span>
                <input type="number" step="any" name="amount" class="form-control" id="inputAmount">
            </div>
                </div>
        </div>

          <div class="form-group">
              <label for="inputDate" class="col-sm-3
              control-label">Date</label>
              <div class="col-sm-9">
              <input type="month" name="date" class="form-control"
                     id="inputAmount" />
                  </div>
          </div>
        
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default"
                data-dismiss="modal">Never mind</button>
        <button type="submit" class="btn btn-primary">Save
            allowance</button>
      </div>
        {{Form::close()}}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->