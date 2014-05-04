@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('componentoverview',$component,null))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2>General overview for {{$component->type->type}} "{{{$component->name}}}"</h2>
        @if($parent)
            <h3>Child of {{{$parent->name}}}</h3>
        @endif
        <div class="btn-group">
            <a href="{{URL::Route('editcomponent',$component->id)}}" class="btn btn-default"><span
                    class="glyphicon glyphicon-pencil"></span></a> <a
                href="{{URL::Route('deletecomponent',$component->id)}}" class="btn btn-default btn-danger"><span
                    class="glyphicon glyphicon-trash"></span></a>
        </div>

        <p class="text-info">
            Setting a limit on "{{{$component->name}}}" means that you can't / shouldn't spend more than <em>x</em>
            on / at this {{$component->type->type}}. Nobody's stopping you of course, but you really shouldn't.
        </p>

    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h4>Months</h4>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Month</th>
                <th>Total transactions / transfers</th>
                <th colspan="3">Limit(s)</th>
                <th>Total amount</th>
            </tr>
            @foreach($months as $m)
            <?php $rows = count($m['limits']) > 1 ? count($m['limits']) : 1;?>
            <tr>
                <td rowspan="{{$rows}}"><a href="{{$m['url']}}" title="{{$m['month']}}">{{{$m['title']}}}</a></td>
                <td rowspan="{{$rows}}">{{$m['count']}}</td>
                @if(count($m['limits']) >= 1)
                <td>{{$m['limits'][0]->account->name or '<em>All accounts</em>'}}</td>
                <td>{{mf($m['limits'][0]->amount,true)}}</td>
                <td>
                    <!-- HERE BE BUTTONS -->
                    <div class="btn-group">
                        <a data-toggle="modal" href="{{URL::Route('addcomponentlimit',[$component->id,$m['year'],$m['month']])}}" data-target="#PopupModal" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a>
                        <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('editcomponentlimit',$m['limits'][0]->id)}}" class="btn btn-info btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('deletecomponentlimit',$m['limits'][0]->id)}}" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                </td>
                @else
                <td rowspan="{{$rows}}" colspan="3">
                    <a data-toggle="modal" href="{{URL::Route('addcomponentlimit',[$component->id,$m['year'],$m['month']])}}" data-target="#PopupModal" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a>
                </td>
                @endif
                <td rowspan="{{$rows}}">{{mf($m['sum'],false,true)}}</td>
            </tr>
            @if(count($m['limits']) > 1)
                @foreach($m['limits'] as $i => $limit)
                @if($i > 0)
                <tr>
                    <td>{{$limit->account->name or '<em>All accounts</em>'}}</td>
                    <td>{{mf($limit->amount,true)}}</td>
                    <td>
                        <div class="btn-group">
                            <a data-toggle="modal" href="{{URL::Route('addcomponentlimit',[$component->id,$m['year'],$m['month']])}}" data-target="#PopupModal" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a>
                            <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('editcomponentlimit',$limit->id)}}" class="btn btn-info btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                            <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('deletecomponentlimit',$limit->id)}}" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            @endif

            {{--
            @foreach($m['limits'] as $m)
                <tr>
                    <td>A</td>
                    <td>A</td>
                    <td>B</td>
                </tr>
            @endforeach

                @if(isset($m['limit']))
                <td>{{mf($m['limit'],false,true)}}</td>
                <td>
                    <div class="btn-group">
                        <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('editcomponentlimit',[$m['limit-id']])}}" class="btn btn-info btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a data-toggle="modal" data-target="#PopupModal" href="{{URL::Route('deletecomponentlimit',[$m['limit-id']])}}" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                </td>
                @else
                    <td colspan="2">
                        <a data-toggle="modal" href="{{URL::Route('addcomponentlimit',[$component->id,$m['year'],$m['month']])}}" data-target="#PopupModal" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a>
                    </td>
                @endif
                @foreach($m['limits'] as $m)
                <tr>
                    <td>X</td>
                </tr>

                @endforeach
                @if(isset($m['limit']) && ($m['sum']*-1) > $m['limit'])
                    <td class="danger">{{mf($m['sum'],false,true)}}</td>
                @else
                    <td>{{mf($m['sum'],false,true)}}</td>
                @endif
                --}}
            @endforeach
        </table>
    </div>
</div>
@stop
