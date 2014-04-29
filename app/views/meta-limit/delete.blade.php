<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete {{$component->type->type}}limit for
            {{{$component->name}}} in month {{$date->format('F Y')}}</h4>
      </div>
        {{Form::open(['class' => 'form-inline','role' => 'form'])}}
      <div class="modal-body">
        <p>
        Are you sure?
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">YES</button>
      </div>
        {{Form::close()}}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->