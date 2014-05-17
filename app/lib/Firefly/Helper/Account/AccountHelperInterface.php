<?php

namespace Firefly\Helper\Account;

interface AccountHelperInterface
{
    public function balanceOnDate(\Account $account, \Carbon\Carbon $date = null);

    public function predictOnDate(\Account $account, \Carbon\Carbon $date);

    public function predictionInformation(\Account $account, \Carbon\Carbon $date);

}