<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete allowance for
            {{$setting->date->format('F Y')}}
            </h4>
      </div>
        {{Form::open(['class' => 'form-inline','role' => 'form'])}}
      <div class="modal-body">
        <p>
        Are you sure you want to delete this allowance? {{$setting->date->format
            ('F Y')}} will fall back to the default allowance.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default"
                data-dismiss="modal">Never mind</button>
        <button type="submit" class="btn btn-danger">YES</button>
      </div>
        {{Form::close()}}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->