<?php


namespace Firefly\Storage\Account;


interface AccountRepositoryInterface
{


    public function all();

    public function allInactive();

    public function selectList();


    public function find($id);

    public function create($input);

    public function initialize($input);


} 