<table class="table table-condensed table-bordered">
    @foreach($rows as $r)
    <tr>
        <th>
            <a href="{{$r['url']}}" title="{{$r['title']}}">{{$r['title']}}</a>
        </th>
    </tr>
    <tr>
        <td>
            @if(isset($r['limit']))
            <!-- BUDGET WITH A LIMIT: -->
            <div class="progress progress-striped">

                @if($r['limit']['over'] == true)
                <!-- BUDGET OVERSPENT! -->
                <!-- BAR TO INDICATE CURRENT SPENDING -->
                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{$r['limit']['amount']}}" aria-valuemin="0" aria-valuemax="{{$r['limit']['amount']}}" style="width: {{round(($r['limit']['amount']/$r['limit']['spent'])*100)}}%;" title="{{mf($r['limit']['amount'])}}">
                    <span>{{mf($r['limit']['spent'])}}</span><span class="sr-only">{{round(($r['limit']['amount']/$r['limit']['spent'])*100)}}% spent</span>
                </div>

                <!-- BAR TO INDICATE OVERSPENDING -->
                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{$r['limit']['spent']-$r['limit']['amount']}}" aria-valuemin="0" aria-valuemax="{{$r['limit']['spent']-$r['limit']['amount']}}" style="width: {{100-round(($r['limit']['amount']/$r['limit']['spent'])*100)}}%;">
                    <span class="sr-only">{{100-round(($r['limit']['amount']/$r['limit']['spent'])*100)}}% overspent</span>
                </div>
                @else
                <!-- BAR TO INDICATE CURRENT SPENDING AS PCT OF LIMIT: -->
                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$r['limit']['spent']}}" aria-valuemin="0" aria-valuemax="{{$r['limit']['spent']}}" style="width: {{round(($r['limit']['spent']/$r['limit']['amount'])*100)}}%;" title="{{mf($r['limit']['spent'])}}">
                    <span>{{mf($r['limit']['spent'])}}</span><span class="sr-only">{{round(($r['limit']['spent']/$r['limit']['amount'])*100)}}% spent</span>
                </div>
                @endif
            </div>
            @else
            <!-- BUDGET WITHOUT LIMIT -->
            <div class="progress progress-striped">
                <!-- BAR TO INDICATE CURRENT SPENDING -->
                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" title="{{mf($r['amount'])}}">
                    <span>{{mf($r['amount']*-1)}}</span><span class="sr-only">no limits</span>
                </div>
                </div>
            @endif
        </td>
        </tr>

        <!--<td>
                <a href="{{$r['url']}}">{{$r['title']}}</a>
        </td>
        <td>
            {{mf($r['amount'],true)}}
        </td>
    </tr>-->
    @endforeach
</table>