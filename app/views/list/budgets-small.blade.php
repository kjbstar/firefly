<table class="table table-bordered">
<?php $sum=0;$limit=0;$left=0;$sumBudgetted=0;$leftBudgetted=0;?>
@foreach($budgets as $id => $budget)
    <?php $sum+=$budget['expense'];$limit+=$budget['limit'];$left+=($budget['limit']+$budget['expense']);?>
    <?php if($id!=0) {$sumBudgetted+=$budget['expense'];$leftBudgetted+=($budget['limit']+$budget['expense']);}?>
    <tr>
        <td colspan="2">
            {{$budget['iconTag']}}
            <small style="font-weight: normal">{{{$budget['parentName'] or ''}}}</small>
            @if($id != 0)
            <a href="{{URL::Route('componentoverview',$id)}}/{{$today->format('Y')}}/{{$today->format('m')}}" title="Overview for {{{$budget['name']}}}">{{{$budget['name']}}}</a>
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
        <td colspan="4">
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
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: {{$budget['pct']}}%;">
                        @if($budget['pct'] > 40)
                        {{mf($budget['expense']*-1)}}
                        @endif
                    </div>
                    @if($budget['pct'] <= 40)
                    <small style="color:#555;">&nbsp;{{mf($budget['expense']*-1)}}</small>
                    @endif
                </div>
            @else
                <!-- NOT OVERSPENT BUT ALSO NO LIMIT -->
                <div class="progress">
                    <div class="progress-bar progress-bar-info" role="progressbar" style="width: 100%;">{{mf($budget['expense']*-1)}}</div>
                </div>
            @endif
            @endif
        </td>
    </tr>
    @endif
    @endforeach
    <tr>
        <td><em>Sums</em></td>
        <td>{{mf($sum*-1,false)}}</td>
        <td>{{mf($limit,true)}}</td>
        <td>{{mf($left,true)}}</td>
    </tr>
    <tr>
        <td><em>Sums (only budgetted)</em></td>
        <td>{{mf($sumBudgetted*-1,false)}}</td>
        <td>{{mf($limit,true)}}</td>
        <td>{{mf($leftBudgetted,true)}}</td>
    </tr>
</table>
