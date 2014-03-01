
@foreach($transactions as $t)
<?php
$t->rowspan = 1;
if(count($t->components) > 1) {
    $t->rowspan += count($t->components);
}
if(!is_null($t->predictable_id)) {
    $t->rowspan++;
}
?>
<table class="table table-bordered table-condensed">
    <tr>
        <!-- CELL MET DATE -->
        <td style="width:150px;" rowspan="{{$t->rowspan}}">
        {{$t->date->format(Config::get('firefly.date_format'))}}<br />
            <a href="{{URL::Route('deletetransaction',[$t->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
        </td>
        <!-- CELL MET DESCRIPTION -->
        <td rowspan="{{$t->rowspan}}">
            <a href="{{URL::Route('edittransaction',[$t->id])}}">{{$t->description}}</a><br />
            @if($t->ignoreprediction == 1)
            <span class="glyphicon glyphicon-eye-close" title="Ignored in predictions"></span>
            @endif
            @if($t->ignoreallowance == 1)
                    <span class="glyphicon glyphicon-gift" title="Ignored in
                    allowance"></span>
            @endif
            @if($t->mark == 1)
            <span class="glyphicon glyphicon-ok" title="Marked in charts"></span>
            @endif
        </td>

        <!-- CELL MET AMOUNT -->
        <td style="width:150px;" rowspan="{{$t->rowspan}}">
            {{mf($t->amount,true)}}
        </td>
    </tr>



    @foreach($t->components()->orderBy('type','DESC')->get() as $c)
    <tr>
        <td style="width:300px;">
            <!-- PARENT COMPONENT -->
            <!-- COMPONENT -->
            @if($c->type == 'beneficiary')
            <span class="glyphicon glyphicon-user"></span>
            @endif
            @if($c->type == 'category')
            <span class="glyphicon glyphicon-inbox"></span>
            @endif
            @if($c->type == 'budget')
            <span class="glyphicon glyphicon-euro"></span>
            @endif

            @if(!is_null($c->parentComponent()->first()))
            <a href="{{URL::Route($c->type.'overview',$c->parentComponent()->first()->id)}}" title="Overview for {{$c->parentComponent()->first()->name}}">{{$c->parentComponent()->first()->name}}</a>
            /
            @endif

            <a href="{{URL::Route($c->type.'overview',$c->id)}}" title="Overview for {{$c->name}}">{{$c->name}}</a>
        </td>
    </tr>
    @endforeach

    @if(!is_null($t->predictable_id))
    <tr>
        <td style="width:300px;">
            <span class="glyphicon glyphicon-repeat"></span> <a href="{{URL::Route('predictableoverview',$t->predictable_id)}}" title="Overview for {{$t->predictable->description}}">{{$t->predictable->description}}</a>
        </td>
    </tr>
    @endif
    <!-- edit and delete -->

</table>
@endforeach