<?php


namespace Firefly\Storage\Account;

use Account;

class EloquentAccountRepository implements AccountRepositoryInterface
{
    public function __construct()
    {
    }

    public function all()
    {
        return Account::whereUserId(\Auth::user()->id)->orderBy('inactive')->orderBy('name')->get();
    }

    public function allInactive()
    {
        return Account::whereUserId(\Auth::user()->id)->where('inactive', 1)->orderBy('name')->get();
    }

    public function selectList()
    {
        $accounts = [];
        foreach (Account::whereUserId(\Auth::user()->id)->where('inactive', 0)->orderBy('name')->get() as $a) {
            $accounts[$a->id] = $a->name;
        }

        return $accounts;
    }

    public function find($id)
    {
        return Account::find($id);
    }

    public function create($input)
    {
        return Account::create($input);
    }
    public function initialize($input)
    {
        return new Account($input);
    }
}