<?php
use Carbon\Carbon as Carbon;

/**
 * Class PiggyController
 */
class PiggyController extends BaseController
{

    /**
     * Index for piggies.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }
        // get piggy banks:
        $piggies = Auth::user()->piggybanks()->orderBy('order', 'ASC')->get();
        // get account:
        $account = Auth::user()->accounts()->find($piggyAccount->value);
        $balance = $account->balanceOnDate(new Carbon);

        $totalTarget = 0;
        foreach ($piggies as $pig) {
            $balance -= $pig->amount;
            $totalTarget += floatval($pig->target);
            $pctFilled = $pig->pctFilled();
            $pctLeft = 100 - $pctFilled;
            // calculate the height we need:
            $pig->pctFilled = $pctFilled;
            $pig->pctLeft = $pctLeft;
        }


        return View::make('piggy.index')
            ->with('title', 'Piggy banks')->with('piggies', $piggies)->with('balance', $balance)->with(
                'totalTarget', $totalTarget
            );
    }

    /**
     * Add new piggy
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function add()
    {
        $prefilled = PiggybankHelper::prefilledFromOldInput();

        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = PiggybankHelper::emptyPrefilledAray();
        }

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        return View::make('piggy.add')->with('title', 'Add new piggy bank')->with('prefilled', $prefilled);
    }

    /**
     * Post process piggy adding.
     *
     * @return \Illuminate\Http\RedirectResponse]
     */
    public function postAdd()
    {

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        $max = Auth::user()->piggybanks()->max('order');

        $data = [
            'name'   => Input::get('name'),
            'amount' => 0,
            'target' => floatval(Input::get('target')),
            'order'    => $max
        ];

        $piggy = new Piggybank($data);
        /** @noinspection PhpParamsInspection */
        $piggy->user()->associate(Auth::user());

        $validator = Validator::make($piggy->toArray(), Piggybank::$rules);
        // failed!
        if ($validator->fails()) {
            Session::flash('error', 'Could not add piggy');
            return Redirect::route('addpiggybank')->withErrors($validator)->withInput();
        }
        // save
        $result = $piggy->save();

        // failed again!
        if (!$result) {
            Session::flash('error', 'Could not add piggy');
            return Redirect::route('addpiggybank')->withErrors($validator)->withInput();
        }


        Session::flash('success', 'Piggy bank created');
        return Redirect::to(Session::get('previous'));
    }

    /**
     * Delete piggy.
     *
     * @param Piggybank $pig
     *
     * @return \Illuminate\View\View
     */
    public function delete(Piggybank $pig)
    {

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        return View::make('piggy.delete')->with('piggy', $pig)->with('title', 'Delete piggy bank ' . $pig->name);
    }

    /**
     * Post delete piggy
     *
     * @param Piggybank $pig
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(Piggybank $pig)
    {

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        $pig->delete();
        Session::flash('success', 'Piggy bank deleted.');
        return Redirect::to(Session::get('previous'));
    }


    /**
     * Select a account.
     *
     * @return \Illuminate\View\View
     */
    public function selectAccount()
    {

        $accounts = Auth::user()->accounts()->notInactive()->get();
        $accountList = [];
        foreach ($accounts as $account) {
            $accountList[$account->id] = $account->name;
        }

        return View::make('piggy.select')->with('title', 'Piggy banks')->with('accounts', $accountList);
    }

    /**
     * Process account selection.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Post edit piggy bank.
     *
     * @param Piggybank $pig
     *
     * @return \Illuminate\View\View
     */
    public function edit(Piggybank $pig)
    {

        $order = PiggybankHelper::getOrders();

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = PiggybankHelper::prefilledFromPiggybank($pig);
        } else {
            $prefilled = PiggybankHelper::prefilledFromOldInput();

        }

        return View::make('piggy.edit')->with('pig', $pig)->with('title', 'Edit piggy bank "' . $pig->name . '"')->with(
            'prefilled', $prefilled
        )->with('order', $order);
    }

    /**
     * Post edit piggy bank
     *
     * @param Piggybank $pig
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Piggybank $pig)
    {
        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }


        $pig->name = Input::get('name');
        $pig->amount = floatval(Input::get('amount'));

        // move everything on position $order and higher one place up
        // to make room for this one.

        $target = floatval(Input::get('target'));
        if ($target > 0) {
            $pig->target = $target;
        }
        $validator = Validator::make($pig->toArray(), Piggybank::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not edit piggy!');
            return Redirect::route('editpiggy', $pig->id)->withErrors($validator)->withInput();
        }
        $pig->save();
        Session::flash('success', 'Piggy bank updated');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * @param Piggybank $pig
     *
     * @return \Illuminate\View\View
     */
    public function updateAmount(Piggybank $pig)
    {

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

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


        return View::make('piggy.amount')->with('pig', $pig)->with('balance', $balance);
    }

    /**
     * @param Piggybank $pig
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUpdateAmount(Piggybank $pig)
    {

        $piggyAccount = Setting::getSetting('piggyAccount');
        if (intval($piggyAccount->value) == 0) {
            return Redirect::route('piggyselect');
        }

        $amount = floatval(Input::get('amount'));
        $pig->amount += $amount;
        $pig->save();
        Session::flash('success', 'Amount for piggy bank updated.');

        return Redirect::to(Session::get('previous'));
    }

    public function dropPiggy()
    {
        $piggyId = intval(Input::get('id'));
        $position = intval(Input::get('position'));
        $pig = Auth::user()->piggybanks()->find($piggyId);
        $pig->order = $position;
        $pig->save();
    }

} 