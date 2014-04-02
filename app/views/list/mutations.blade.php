<table class="table table-bordered table-condensed">
    <tr>
        <th>Date</th>
        <th colspan="2">Description</th>
        <th>Amount</th>
        <th>Account</th>
        <th>Beneficiary</th>
        <th>Budget</th>
        <th>Category</th>

        <th>&nbsp;</th>
    </tr>

    @foreach($mutations as $t)
    <?php
    // prep some vars to save on queries:
    $ben = $t->beneficiary;
    $bud = $t->budget;
    $cat = $t->category;
    $pBen = !is_null($ben) && !is_null($ben->parent_component_id) ? $ben->parentComponent()->first() : null;
    $pBud = !is_null($bud) && !is_null($bud->parent_component_id) ? $bud->parentComponent()->first() : null;
    $pCat = !is_null($cat) && !is_null($cat->parent_component_id) ? $cat->parentComponent()->first() : null;
    $hasDate = isset($date) ? true : false;
    $class = strtolower(get_class($t));

    ?>

    <tr>
        <td>{{$t->date->format('d-m-y')}}</td>
        @if($t->ignoreprediction == 0 && $t->ignoreallowance == 0 && $t->mark == 0 && is_null($t->predictable_id))
        <td colspan="2">
            @else
        <td>
            @endif
            <a href="{{URL::Route('edit'.$class,[$t->id])}}">{{{$t->description}}}</a></td>
        @if($t->ignoreprediction == 1 || $t->ignoreallowance == 1 || $t->mark == 1 || !is_null($t->predictable_id))
        <td>
            @if($t->ignoreprediction == 1)
            <span class="glyphicon glyphicon-eye-close" title="Ignored in predictions"></span>
            @endif
            @if($t->ignoreallowance == 1)
            <span class="glyphicon glyphicon-gift" title="Ignored in allowance"></span>
            @endif
            @if($t->mark == 1)
            <span class="glyphicon glyphicon-ok" title="Marked in charts"></span>
            @endif
            @if(!is_null($t->predictable_id))
            <a href="{{URL::Route('predictableoverview',$t->predictable_id)}}" title="{{{$t->predictable->description}}}"><span class="glyphicon glyphicon-repeat" title="{{{$t->predictable->description}}}"></span></a>
            @endif
        </td>
        @endif

        <td>{{mf($t->amount,true)}}</td>
        <td>
            @if($class == 'transaction')
                <a href="{{URL::Route('accountoverview',$t->account_id)}}">{{{$t->account()->first()->name}}}</a>
            @else
            <a href="{{URL::Route('accountoverview',$t->accountfrom_id)}}">{{{$t->accountfrom()->first()->name}}}</a>
            &rarr;
            <a href="{{URL::Route('accountoverview',$t->accountto_id)}}">{{{$t->accountto()->first()->name}}}</a>
            @endif

        </td>
        <td>
            @if($ben)
            <!-- show parent if exists -->
            @if(!is_null($pBen))
            <!-- date specific URL if relevant -->
            @if($hasDate)
            <small><a href="{{URL::Route('beneficiaryoverview',[$pBen->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$pBen->name}}} in {{$date->format('F Y')}}">{{{$pBen->name}}}</a></small><br />
            @else
            <small><a href="{{URL::Route('beneficiaryoverview',[$pBen->id])}}" title="Overview for {{{$pBen->name}}}}}">{{{$pBen->name}}}</a></small><br />
            @endif
            @endif
            <!-- show component if exists: -->
            @if($hasDate)
            <a href="{{URL::Route('beneficiaryoverview',[$ben->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$ben->name}}} in {{$date->format('F Y')}}">{{{$ben->name}}}</a>
            @else
            <a href="{{URL::Route('beneficiaryoverview',[$ben->id])}}" title="Overview for {{{$ben->name}}}}}">{{{$ben->name}}}</a>
            @endif
            @endif
        </td>
        <td>
            @if($bud)
            <!-- show parent if exists -->
            @if(!is_null($pBud))
            <!-- date specific URL if relevant -->
            @if($hasDate)
            <small><a href="{{URL::Route('budgetoverview',[$pBud->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$pBud->name}}} in {{$date->format('F Y')}}">{{{$pBud->name}}}</a></small><br />
            @else
            <small><a href="{{URL::Route('budgetoverview',[$pBud->id])}}" title="Overview for {{{$pBud->name}}}}}">{{{$pBud->name}}}</a></small><br />
            @endif
            @endif
            <!-- show component if exists: -->
            @if($hasDate)
            <a href="{{URL::Route('budgetoverview',[$bud->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$bud->name}}} in {{$date->format('F Y')}}">{{{$bud->name}}}</a>
            @else
            <a href="{{URL::Route('budgetoverview',[$bud->id])}}" title="Overview for {{{$bud->name}}}}}">{{{$bud->name}}}</a>
            @endif
            @endif
        </td>
        <td>
            @if($cat)
            <!-- show parent if exists -->
            @if(!is_null($pCat))
            <!-- date specific URL if relevant -->
            @if($hasDate)
            <small><a href="{{URL::Route('categoryoverview',[$pCat->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$pCat->name}}} in {{$date->format('F Y')}}">{{{$pCat->name}}}</a></small><br />
            @else
            <small><a href="{{URL::Route('categoryoverview',[$pCat->id])}}" title="Overview for {{{$pCat->name}}}}}">{{{$pCat->name}}}</a></small><br />
            @endif
            @endif
            <!-- show component if exists: -->
            @if($hasDate)
            <a href="{{URL::Route('categoryoverview',[$cat->id,$date->format('Y'),$date->format('m')])}}" title="Overview for {{{$cat->name}}} in {{$date->format('F Y')}}">{{{$cat->name}}}</a>
            @else
            <a href="{{URL::Route('categoryoverview',[$cat->id])}}" title="Overview for {{{$cat->name}}}}}">{{{$cat->name}}}</a>
            @endif
            @endif
        </td>

        <td><a href="{{URL::Route('delete'.$class,[$t->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash"></span></a></td>
    </tr>
    @endforeach
</table>