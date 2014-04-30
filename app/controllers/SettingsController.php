<?php
use Carbon\Carbon as Carbon;

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/AccountHelper.php');


/**
 * Class SettingsController
 */
class SettingsController extends BaseController
{

    /**
     * Show the index for the settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        // let's grab the settings that might be available.
        $predictionStart = Toolkit::getPredictionStart();
        $frontpageAccount = Toolkit::getFrontpageAccount();

        // and the setting that controls which accounts (and
        // subsequent predictions) you want to see on the front page:
        $accountList = [];
        foreach (Auth::user()->accounts()->get() as $a) {
            $accountList[$a->id] = $a->name;
        }

        return View::make('settings.index')->with('title', 'Settings')->with('predictionStart', $predictionStart)->with(
            'accountList', $accountList
        )->with('frontpageAccount', $frontpageAccount);

    }

    /**
     * Post function for the settings: saves them.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex()
    {
        // save all settings. For now, just the predictionStart one.
        $predictionStart = Setting::findSetting('predictionStart');
        $frontpageAccount = Setting::findSetting('frontpageAccount');

        $predictionStart->value = Input::get('predictionStart');
        $frontpageAccount->value = Input::get('frontpageAccount');

        $predictionStart->save();
        $frontpageAccount->save();


        Session::flash('success', 'Settings saved!');

        return Redirect::to(Session::get('previous'));

    }

    /**
     * Show the index for allowances stuff.
     *
     * @return \Illuminate\View\View
     */
    public function allowances()
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $defaultAllowance->value = floatval($defaultAllowance->value);


        // specific allowances:
        $allowances = Auth::user()->settings()->with('account')->orderBy('date', 'ASC')->where(
            'name', 'specificAllowance'
        )->get();

        return View::make('settings.allowances')->with('defaultAllowance', $defaultAllowance)->with(
            'allowances', $allowances
        )->with('title', 'Allowances');
    }

    /**
     * The post function only saves the default allowance amount.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAllowances()
    {
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $defaultAllowance->value = floatval(Input::get('defaultAllowance'));
        $defaultAllowance->type = 'float';
        $defaultAllowance->save();
        Session::flash('success', 'Default allowance saved!');

        return Redirect::to(Session::get('previous'));
    }


    /**
     * Add a allowance.
     *
     * @return \Illuminate\View\View
     */
    public function addAllowance()
    {

        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('settings.add-allowance')->with('title', 'Add a new allowance')->with('accounts', $accounts);
    }

    /**
     * Post add allowance.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddAllowance()
    {
        $date = new Carbon(Input::get('date') . '-01');
        $amount = floatval(Input::get('amount'));

        $setting = new Setting;


        $setting->date = $date;
        $setting->value = $amount;
        $setting->type = 'float';
        $setting->name = 'specificAllowance';
        /** @noinspection PhpParamsInspection */
        $setting->user()->associate(Auth::user());
        if (!is_null(Input::get('account_id'))) {
            $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
            if (is_null($account)) {
                Session::flash('error', 'Invalid account selected.');
                return Redirect::to(Session::get('previous'));
            }
            $setting->account()->associate($account);
        }

        // validate
        $validator = Validator::make($setting->toArray(), Setting::$rules);
        if ($validator->fails() || $amount == 0) {
            Session::flash('error', $validator->messages()->all());
            return Redirect::to(Session::get('previous'));
        } else {
            Session::flash('success', 'Allowance saved!');
            $setting->save();
        }

        return Redirect::to(Session::get('previous'));


    }

    /**
     * Edit a allowance
     *
     * @param Setting $setting
     *
     * @return \Illuminate\View\View
     */
    public function editAllowance(Setting $setting)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('settings.edit-allowance')->with('setting', $setting)->with('accounts', $accounts);
    }

    /**
     * Post edit allowance.
     *
     * @param Setting $setting
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditAllowance(Setting $setting)
    {
        $setting->value = floatval(Input::get('value'));
        $validator = Validator::make($setting->toArray(), Setting::$rules);
        if ($validator->fails() || floatval(Input::get('value')) == 0) {
            Session::flash('error', 'Because of an error, the allowance could not be added.');
        } else {
            $setting->save();
            Session::flash('success', 'Allowance for ' . $setting->date->format('F Y') . ' has been saved.');
        }

        return Redirect::to(Session::get('previous'));
    }

    /**
     * Delete allowance.
     *
     * @param Setting $setting
     *
     * @return \Illuminate\View\View
     */
    public function deleteAllowance(Setting $setting)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('settings.delete-allowance')->with(
            'setting', $setting
        );
    }

    /**
     * Post delete allowance.
     *
     * @param Setting $setting
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteAllowance(Setting $setting)
    {
        $setting->delete();
        Session::flash('success', $setting->date->format('F Y') . ' no longerhas a specific allowance');

        return Redirect::to(Session::get('previous'));
    }

}