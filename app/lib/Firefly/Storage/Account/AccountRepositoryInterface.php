<?php


namespace Firefly\Storage\Account;


interface AccountRepositoryInterface
{


    public function all();


    public function find($id);

    public function create($input);

} 