<?php
use Carbon\Carbon as Carbon;

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
        Session::put('previous', URL::previous());

        // let's grab the only setting that might be available.
        $predictionStart = Setting::getSetting('predictionStart');

        // let's also grab the 'extendedReporting' setting that
        // contains components for comparision.
        $extendedReporting = Setting::getSetting('extendedReporting');
        $selectedComponents = explode(',', $extendedReporting->value);
        $componentList = [];

        $components = Auth::user()->components()->get();
        foreach ($components as $component) {
            $type = ucfirst(Str::plural($component->type));
            $componentList[$type] = isset($componentList[$type])
                ? $componentList[$type] : [];

            $componentList[$type][$component->id] = $component->name;
        }
        asort($componentList['Beneficiaries'], SORT_STRING);
        asort($componentList['Budgets'], SORT_STRING);
        asort($componentList['Categories'], SORT_STRING);

        // and the setting that controls which accounts (and
        // subsequent predictions) you want to see on the front page:
        $frontpageAccounts = Setting::getSetting('frontpageAccounts');
        $selectedAccounts = explode(',', $frontpageAccounts->value);
        $accountList = [];
        foreach (Auth::user()->accounts()->get() as $a) {
            $accountList[$a->id] = $a->name;
        }


        return View::make('settings.index')->with('title', 'Settings')->with(
            'predictionStart', $predictionStart
        )->with('extendedReporting', $extendedReporting)->with(
                'componentList', $componentList
            )->with('selectedComponents', $selectedComponents)->with(
                'accountList', $accountList
            )->with(
                'selectedAccounts', $selectedAccounts
            );

    }

    /**
     * Post function for the settings: saves them.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex()
    {
        // save all settings. For now, just the predictionStart one.
        $predictionStart = Setting::getSetting('predictionStart');
        $predictionStart->value = Input::get('predictionStart');
        $predictionStart->save();


        $inputComponents = is_array(Input::get('extendedReporting'))
            ? Input::get('extendedReporting') : [];
        $selectedComponents = [];
        foreach ($inputComponents as $id) {
            if (Auth::user()->components()->find($id)) {
                $selectedComponents[] = $id;
            }
        }

        $inputAccounts = is_array(Input::get('frontpageAccounts')) ? Input::get(
            'frontpageAccounts'
        ) : [];
        $selectedAccounts = [];
        foreach ($inputAccounts as $id) {
            if (Auth::user()->accounts()->find($id)) {
                $selectedAccounts[] = $id;
            }
        }

        $extendedReporting = Setting::getSetting('extendedReporting');
        $extendedReporting->value = join(',', $selectedComponents);
        $extendedReporting->save();

        $frontpageAccounts = Setting::getSetting('frontpageAccounts');
        $frontpageAccounts->value = join(',', $selectedAccounts);
        $frontpageAccounts->save();

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
        Cache::flush();
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $defaultAllowance->value = floatval($defaultAllowance->value);

        // specific allowances:
        $allowances = Auth::user()->settings()->orderBy(
            'date', 'ASC'
        )->where(
                'name', 'specificAllowance'
            )->get();

        return View::make('settings.allowances')->with(
            'defaultAllowance', $defaultAllowance
        )->with('allowances', $allowances);
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


    public function addAllowance()
    {
        Session::put('previous', URL::previous());

        return View::make('settings.add-allowance');
    }

    public function postAddAllowance()
    {
        $date = new Carbon(Input::get('date') . '-01');
        $amount = floatval(Input::get('amount'));

        $setting = new Setting;
        $setting->user()->associate(Auth::user());
        $setting->date = $date;
        $setting->value = $amount;
        $setting->type = 'float';
        $setting->name = 'specificAllowance';
        // validate
        $validator = Validator::make($setting->toArray(), Setting::$rules);
        if ($validator->fails()) {
            Session::flash(
                'warning', 'Because of an error,
            the allowance could not be added.'
            );

            return Redirect::to(Session::get('previous'));
        } else {
            $setting->save();
        }

        return Redirect::to(Session::get('previous'));


    }

    public function editAllowance(Setting $setting)
    {
        Session::put('previous', URL::previous());

        return View::make('settings.edit-allowance')->with(
            'setting', $setting
        );
    }

    public function postEditAllowance(Setting $setting)
    {
        $setting->value = floatval(Input::get('value'));
        $setting->save();
        Session::flash(
            'success', 'Allowance for ' . $setting->date->format('F Y') . ' has been
            saved.'
        );

        return Redirect::to(Session::get('previous'));
    }

    public function deleteAllowance(Setting $setting)
    {
        Session::put('previous', URL::previous());

        return View::make('settings.delete-allowance')->with(
            'setting', $setting
        );
    }

    public function postDeleteAllowance(Setting $setting)
    {
        $setting->delete();
        Session::flash(
            'success', $setting->date->format('F Y') . ' no longer
        has a specific allowance'
        );

        return Redirect::to(Session::get('previous'));
    }

}