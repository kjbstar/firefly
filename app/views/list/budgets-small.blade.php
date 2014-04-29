<table class="table table-bordered">

@foreach($budgets as $id => $budget)
    <tr>
        <td>
            {{$budget['iconTag']}}
            <small style="font-weight: normal">{{{$budget['parentName'] or ''}}}</small>
            @if($id != 0)
            <a href="{{URL::Route('componentoverview',$id)}}" title="Overview for {{{$budget['name']}}}">{{{$budget['name']}}}</a>
            @else
            <a href="{{URL::Route('empty',[3,$today->format('Y'),$today->format('m')])}}" title="Overview for {{{$budget['name']}}}">{{{$budget['name']}}}</a>
            @endif
        </td>
        <td style="width:18%;">
            <span style="font-size:90%;">{{mf($budget['limit'],true)}}</span>
        </td>
        <td style="width:18%;">
            <span style="font-size:90%;">{{mf($budget['limit']+$budget['expense'],true)}}</span>
        </td>
    </tr>
    @if($budget['expense'] != 0)
    <tr>
        <td colspan="3">
            <!-- OVERSPENT BUDGETS: -->
            @if($budget['overspent'])
            <div class="progress">
                <div class="progress-bar progress-bar-warning" role="progressbar" style="width: {{$budget['pct']}}%;">{{mf($budget['expense']*-1)}}</div>
                <div class="progress-bar progress-bar-danger" role="progressbar" style="width: {{100-$budget['pct']}}%;"></div>
            </div>
            @else
            @if($budget['limit'])
                <!-- NOT OVERSPENT BUT HAS LIMIT -->
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: {{$budget['pct']}}%;">{{mf($budget['expense']*-1)}}</div>
                </div>
            @else
                <!-- NOT OVERSPENT BUT ALSO NO LIMIT -->
                <div class="progress">
                    <div class="progress-bar progress-bar-info" role="progressbar" style="width: 100%;">{{mf($budget['expense']*-1)}}</div>
                </div>
            @endif
            @endif

            {{--
            @if(isset($budget['limit']) && $budget['limit'] < $budget['spent'])
            <!-- overspent bar -->
            <div class="progress">
                <div class="progress-bar progress-bar-warning" role="progressbar" style="width: {{$budget['pct']}}%;">{{mf($budget['spent'])}}</div>
                <div class="progress-bar progress-bar-danger" role="progressbar" style="width: {{100-$budget['pct']}}%;"></div>
            </div>
            @elseif(isset($budget['limit']) && $budget['limit'] >= $budget['spent'])
            <!-- normal bar -->
            <div class="progress">
                <div class="progress-bar progress-bar-success" role="progressbar" style="width: {{$budget['pct']}}%;">{{mf($budget['spent'])}}</div>
            </div>
            @elseif(!isset($budget['limit']))
            <!-- full blue bar -->
            <div class="progress">
                <div class="progress-bar progress-bar-info" role="progressbar" style="width: 100%;">{{mf($budget['spent'])}}</div>
            </div>
            @endif
            --}}
        </td>
    </tr>
    @endif
    @endforeach
</table>
