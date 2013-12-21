<nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{URL::Route('home')}}">Firefly // {{date('F Y')}}</a>
        </div>
        <ul class="nav navbar-nav">
              <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Lists <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="{{URL::Route('accounts')}}">Accounts</a></li>
              <li><a href="{{URL::Route('beneficiaries')}}">Beneficiaries</a></li>
              <li><a href="{{URL::Route('budgets')}}">Budgets</a></li>
              <li><a href="{{URL::Route('categories')}}">Categories</a></li>
              <li><a href="{{URL::Route('transactions')}}">Transactions</a></li>
              <li><a href="{{URL::Route('transfers')}}">Transfers</a></li>
            </ul>
          </li>
            <li><a href="{{URL::Route('settings')}}">Settings</a></li>
        </ul>

        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Logout</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </nav>