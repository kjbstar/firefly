<?php


namespace Firefly\Filter;


class AccountFilter
{

    public function addAccount()
    {
        if (\Input::old()) {
            \Session::put('account', $this->fromOldInput());
        } else {
            \Session::put('previous', \URL::previous());
            \Session::put(
                'account', [
                    'name'               => '',
                    'openingbalance'     => '',
                    'openingbalancedate' => date('Y-m-d'),
                    'inactive'           => false,
                    'shared'             => false
                ]
            );
        }
    }

    public function editAccount(
        \Illuminate\Routing\Route $route = null, \Illuminate\Http\Request $request = null, $value = null
    ) {

        /** @var $account \Account */
        $account = $route->parameters()['account'];
        if (\Input::old()) {
            \Session::put('account', $this->fromOldInput());
        } else {
            \Session::put('previous', \URL::previous());
            \Session::put(
                'account', [
                    'name'               => $account->name,
                    'openingbalance'     => $account->openingbalance,
                    'openingbalancedate' => $account->openingbalancedate->format('Y-m-d'),
                    'inactive'           => $account->inactive == 1 ? true : false,
                    'shared'             => $account->shared == 1 ? true : false,

                ]
            );
        }
    }

    private function fromOldInput()
    {
        return [
            'name'               => \Input::old('name'),
            'openingbalance'     => \Input::old('openingbalance'),
            'openingbalancedate' => \Input::old('openingbalancedate'),
            'inactive'           => intval(\Input::old('inactive')) == 1 ? true : false,
            'shared'             => intval(\Input::old('shared')) == 1 ? true : false
        ];
    }
}