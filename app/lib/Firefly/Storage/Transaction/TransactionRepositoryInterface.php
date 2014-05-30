<?php


namespace Firefly\Storage\Transaction;


interface TransactionRepositoryInterface {

    public function predictionBase(\Account $account, \Carbon\Carbon $date);

} 