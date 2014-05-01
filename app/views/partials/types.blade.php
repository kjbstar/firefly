<!-- all component types in a loop! -->
@foreach(Type::allTypes() as $i => $type)
<div class="form-group">
    <label for="input{{$type->type}}" class="col-sm-3 control-label">{{ucfirst($type->type)}}</label>
    <div class="col-sm-9">
        <input type="text" value="{{{$prefilled[$type->type] or ''}}}"
               name="{{$type->type}}" class="form-control"
               @if($i == 0)
                placeholder="Simply type to create a new {{$type->type}}..."
               @endif
               @if($i == 1)
                placeholder=".. or select existing {{Str::plural($type->type)}} from the list..."
                @endif

               id="input{{$type->type}}" autocomplete="off" />
    </div>
</div>
@endforeach