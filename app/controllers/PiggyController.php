<?php
use Carbon\Carbon as Carbon;

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/PiggybankHelper.php');


/**
 * Class PiggyController
 */
class PiggyController extends BaseController
{

    public static $pigWidth = 252;
    public static $pigHeight = 200;

    public function index()
    {
        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }
        // get piggy banks:
        $piggies = Auth::user()->piggybanks()->get();
        // get account:
        $account = Auth::user()->accounts()->find($piggyAccount->value);
        $balance = $account->balanceOnDate(new Carbon);

        foreach ($piggies as $pig) {
            $balance -= $pig->amount;
            $pct_filled = $pig->pctFilled();
            $pct_left = 100 - $pct_filled;
            // heigth of animation
            $step = $this::$pigHeight / 100;
            // calculate the height we need:
            $drawHeight = $pct_left * $step;

            $pig->drawHeight = $drawHeight;
        }


        return View::make('piggy.index')->with('pigWidth', $this::$pigWidth)
            ->with('pigHeight', $this::$pigHeight)->with('title', 'Piggy banks')

            ->with(
                'piggies', $piggies
            )->with('balance', $balance);
    }

    public function add()
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = PiggybankHelper::emptyPrefilledAray();
        } else {
            $prefilled = PiggybankHelper::prefilledFromOldInput();
        }


        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        return View::make('piggy.add')->with('title', 'Add new piggy bank')->with('prefilled', $prefilled);
    }

    public function postAdd()
    {
        $piggy = new Piggybank;
        /** @noinspection PhpParamsInspection */
        $piggy->user()->associate(Auth::user());
        $piggy->name = Input::get('name');
        $piggy->amount = 0;
        $target = is_null(Input::get('target'))
        || intval(
            Input::get
                (
                    'target'
                )
        ) == 0
            ? null
            : floatval(
                Input::get('target')
            );
        $piggy->target = $target;
        $validator = Validator::make($piggy->toArray(), Piggybank::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not add piggy');
            return Redirect::route('addpiggybank')->withErrors(
                $validator
            )->withInput();
        }
        $piggy->save();
        Session::flash('success', 'Piggy bank created');

        return Redirect::to(Session::get('previous'));
    }

    public function delete(Piggybank $pig)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        return View::make('piggy.delete')->with('piggy', $pig)->with('title', 'Delete piggy bank ' . $pig->name);
    }

    public function postDelete(Piggybank $pig)
    {
        $pig->delete();
        Session::flash('success', 'Piggy bank deleted.');
        return Redirect::to(Session::get('previous'));
    }

    public function selectAccount()
    {

        $accounts = Auth::user()->accounts()->notHidden()->get();
        $accountList = [];
        foreach ($accounts as $account) {
            $accountList[$account->id] = $account->name;
        }

        return View::make('piggy.select')->with(
            'title', 'Piggy banks'
        )->with('accounts', $accountList);
    }

    public function postSelectAccount()
    {
        $piggyAccount = Setting::getSetting('piggyAccount');
        $account = Auth::user()->accounts()->find(Input::get('account'));
        if ($account) {
            $piggyAccount->value = $account->id;
            $piggyAccount->save();
            Session::flash('success', 'Account selected.');
            return Redirect::route('piggy');
        } else {
            Session::flash('error', 'Invalid account');
            return Redirect::route('piggyselect');
        }


    }

    public function edit(Piggybank $pig)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = PiggybankHelper::prefilledFromPiggybank($pig);
        } else {
            $prefilled = PiggybankHelper::prefilledFromOldInput();

        }

        return View::make('piggy.edit')->with('pig', $pig)->with(
            'title', 'Edit piggy bank "' . $pig->name . '"'
        )->with('prefilled',$prefilled);
    }

    public function postEdit(Piggybank $pig)
    {
        $pig->name = Input::get('name');
        $pig->amount = floatval(Input::get('amount'));
        $target = floatval(Input::get('target'));
        if ($target > 0) {
            $pig->target = $target;
        }
        $validator = Validator::make($pig->toArray(), Piggybank::$rules);
        if ($validator->fails()) {
            return Redirect::route('editpiggy', $pig->id)->withErrors(
                $validator
            )->withInput();
        }
        $pig->save();
        Session::flash('success', 'Piggy bank updated');

        return Redirect::to(Session::get('previous'));
    }

    public function updateAmount(Piggybank $pig)
    {
        // calculate the amount of money left to devide:
        $piggies = Auth::user()->piggybanks()->get();
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        // get account:
        $piggyAccount = Setting::getSetting('piggyAccount');
        $account = Auth::user()->accounts()->find($piggyAccount->value);
        $balance = $account->balanceOnDate(new Carbon);

        foreach ($piggies as $current) {
            $balance -= $current->amount;
        }


        return View::make('piggy.amount')->with('pig', $pig)->with(
            'balance', $balance
        );
    }

    public function postUpdateAmount(Piggybank $pig)
    {
        $amount = floatval(Input::get('amount'));
        $pig->amount += $amount;
        $pig->save();
        Session::flash('success', 'Amount for piggy bank updated.');

        return Redirect::to(Session::get('previous'));
    }

} 