<?php

namespace api\v1;


use Carbon\Carbon;

class AccountController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $accounts = \Auth::user()->accounts()->get();

        return \Response::json(
            array('error' => false, 'accounts' => $accounts->toArray()), 200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \Response::json(
            array('error'   => true,
                  'message' => 'This resource is not available through the API.',

            ), 501
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $account = new \Account;
        $account->name = Request::get('name');
        $account->openingbalance = Request::get('openingbalance');
        $account->openingbalancedate = new Carbon(Request::get(
            'openingbalancedate'
        ));
        $account->currentbalance = Request::get('openingbalance');
        $account->hidden = intval(Request::get('name')) == 1 ? 1 : 0;

        $validator = \Validator::make($account->toArray(), \Account::$rules);
        if ($validator->fails()) {
            return \Response::json(
                array('error'   => true,
                      'message' => $validator->messages()->all()->toArray()),
                200
            );
        } else {
            $result = $account->save();
            if (!$result) {
                return \Response::json(
                    array('error' => true, 'message' => 'Invalid account name'),
                    200
                );
            } else {
                return \Response::json(
                    array('error' => false, 'accounts' => $account->toArray()),
                    200
                );
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        return \Response::json(
            array('error'   => true,
                  'message' => 'This resource is not available through the API.',

            ), 501
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}