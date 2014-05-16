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

    public function find($id)
    {
        return Account::find($id);
    }

    public function create($input)
    {
        return Account::create($input);
    }
}