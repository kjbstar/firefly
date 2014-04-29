<?php
$types = Type::allTypes();
?>

<nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{URL::Route('home')}}">Firefly //
              @if(isset($today))
              {{$today->format('F Y')}}
              @else
              {{date('F Y')}}
              @endif
          </a>
        </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            @if(Route::getCurrentRoute()->getName() == 'home')
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{{$fpAccount->name}}} <b class="caret"></b></a>
            <ul class="dropdown-menu">
                @foreach($accounts as $account)
                    @if($account['name'] != $fpAccount->name)
                        <li><a href="{{$account['homeurl']}}">{{{$account['name']}}}</a></li>
                    @endif
                @endforeach
            </ul>
            </li>
            @endif
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Add
                    <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{URL::Route('addaccount')}}"><span
                                class="glyphicon glyphicon-plus"></span> Add
                            account
                        </a></li>
                    @foreach($types as $type)
                    <li><a href="{{URL::Route('addcomponent',$type->id)}}"><span
                                class="glyphicon glyphicon-plus"></span>
                            Add {{$type->type}}</a></li>
                    @endforeach

                    <li><a href="{{URL::Route('addpredictable')}}"><span
                                class="glyphicon glyphicon-plus"></span>
                            Add predictable</a></li>
                    <li><a href="{{URL::Route('addtransaction')}}"><span
                                class="glyphicon glyphicon-plus"></span>
                            Add transaction</a></li>
                    <li><a href="{{URL::Route('addtransfer')}}"><span
                                class="glyphicon glyphicon-plus"></span> Add
                            transfer
                        </a></li>
                    </ul>
                    </li>

              <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Lists <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="{{URL::Route('accounts')}}"><span class="glyphicon
              glyphicon-list"></span> Accounts</a></li>
                @foreach($types as $type)
                <li><a href="{{URL::Route('components',$type->id)}}"><span class="glyphicon
              glyphicon-list"></span> {{ucfirst(Str::plural($type->type))}}</a></li>
                @endforeach

                <li><a href="{{URL::Route('predictables')}}"><span class="glyphicon
              glyphicon-list"></span> Predictables</a></li>
                <li><a href="{{URL::Route('transactions')}}"><span class="glyphicon
              glyphicon-list"></span> Transactions</a></li>
              <li><a href="{{URL::Route('transfers')}}"><span class="glyphicon
              glyphicon-list"></span> Transfers</a></li>
            </ul>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle"
                   data-toggle="dropdown">More <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{URL::Route('settings')}}"><span
                                class="glyphicon glyphicon-cog"></span>
                            Settings</a></li>
                    <li><a href="{{URL::Route('allowances')}}"><span
                                class="glyphicon glyphicon-euro"></span>
                            Allowances</a></li>
                    <li><a href="{{URL::Route('reports')}}"><span
                                class="glyphicon glyphicon-book"></span>
                            Reports</a></li>
                    <li><a href="{{URL::Route('piggy')}}"><span
                                class="glyphicon glyphicon-time"></span>
                            Piggy banks</a></li>

                </ul>
                </li>

        </ul>
        {{Form::open(['action' => 'search', 'class' => 'navbar-form navbar-left','method' => 'get'])}}
        <form class="" role="search">
            <div class="form-group">
                @if(isset($search['originalQuery']))
                <input type="text" name="query" class="form-control" placeholder="Search" value="{{{$search['originalQuery']}}}">
                @else
                <input type="text" name="query" class="form-control" placeholder="Search">
                @endif
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Logout {{{Auth::user()->username}}}</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </nav>