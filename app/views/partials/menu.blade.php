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
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Add
                    <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{URL::Route('addaccount')}}"><span
                                class="glyphicon glyphicon-plus"></span> Add
                            account
                        </a></li>
                    <li><a href="{{URL::Route('addbeneficiary')}}"><span
                                class="glyphicon glyphicon-plus"></span>
                            Add beneficiary</a></li>
                    <li><a href="{{URL::Route('addbudget')}}"><span
                                class="glyphicon
                                glyphicon-plus"></span> Add budget</a></li>
                    <li><a href="{{URL::Route('addcategory')}}"><span
                                class="glyphicon glyphicon-plus"></span>
                            Add category</a></li>
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
              <li><a href="{{URL::Route('beneficiaries')}}"><span class="glyphicon
              glyphicon-list"></span> Beneficiaries</a></li>
              <li><a href="{{URL::Route('budgets')}}"><span class="glyphicon
              glyphicon-list"></span> Budgets</a></li>
              <li><a href="{{URL::Route('categories')}}"><span class="glyphicon
              glyphicon-list"></span> Categories</a></li>
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
                                class="glyphicon glyphicon-euro"></span>
                            Reports</a></li>
                    <li><a href="{{URL::Route('piggy')}}"><span
                                class="glyphicon glyphicon-time"></span>
                            Piggy banks</a></li>

                </ul>
                </li>

        </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Logout</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </nav>