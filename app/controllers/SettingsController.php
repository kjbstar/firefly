<?php
use Carbon\Carbon as Carbon;
use Firefly\Storage\Setting\SettingRepositoryInterface as ASI;
use Firefly\Storage\Account\AccountRepositoryInterface as ARI;

/**
 * Class SettingsController
 */
class SettingsController extends BaseController
{
    public function __construct(ASI $settings,ARI $accounts)
    {
        $this->settings = $settings;
        $this->accounts = $accounts;

    }

    /**
     * Show the index for the settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Session::put('previous', URL::previous());

        // let's grab the settings that might be available.
        $predictionStart = $this->settings->getSettingValue('predictionStart');
        $frontpageAccount = $this->settings->getSettingValue('frontPageAccount');

        // get the available currencies and put them in a list:
        $currencies = [];
        foreach (Config::get('firefly.currencies') as $index => $currency) {
            $currencies[$index] = $currency['name'];
        }
        $currency = $this->settings->getSettingValue('currency');

        // and the setting that controls which accounts (and
        // subsequent predictions) you want to see on the front page:
        $accountList = $this->accounts->selectList();

        return View::make('settings.index')->with('title', 'Settings')->with('predictionStart', $predictionStart)->with(
            'accountList', $accountList
        )->with('frontpageAccount', $frontpageAccount)->with('currencies', $currencies)->with('currency', $currency);

    }

    /**
     * Post function for the settings: saves them.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex()
    {
        $list = ['predictionStart','frontpageAccount','currency'];
        foreach($list as $s) {
            $set = $this->settings->getSetting($s);
            if(is_null($set)) {
                $set = $this->settings->create($s);
            }
            $set->value = Input::get($s);
            $set->save();
        }

        Cache::userFlush();
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
        Session::put('previous', URL::previous());
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

        Session::put('previous', URL::previous());

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

        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            return Redirect::to(Session::get('previous'));
        }

        $setting = new Setting;


        $setting->date = $date;
        $setting->value = $amount;
        $setting->type = 'float';
        $setting->name = 'specificAllowance';
        /** @noinspection PhpParamsInspection */
        $setting->user()->associate(Auth::user());
        $setting->account()->associate($account);

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
        Session::put('previous', URL::previous());
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
        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            return Redirect::to(Session::get('previous'));
        }
        $setting->account()->associate($account);


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
        Session::put('previous', URL::previous());

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