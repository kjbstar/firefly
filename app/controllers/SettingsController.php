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

        // let's grab the only setting that might be available.
        $predictionStart = Setting::getSetting('predictionStart');

        return View::make('settings.index')->with('title', 'Settings')->with(
            'predictionStart', $predictionStart
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
        Session::flash('success', 'Settings saved!');

        return Redirect::route('settings');

    }

    /**
     * Show the index for allowances stuff.
     *
     * @return \Illuminate\View\View
     */
    public function allowances()
    {
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
        $defaultAllowance->save();
        Session::flash('success', 'Default allowance saved!');

        return Redirect::route('allowances');
    }




    public function addAllowance()
    {
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
        $setting->name = 'specificAllowance';
        // validate
        $validator = Validator::make($setting->toArray(), Setting::$rules);
        if ($validator->fails()) {
            return Session::flash(
                'warning', 'Because of an error,
            the allowance could not be added.'
            );
        } else {
            $setting->save();
        }

        return Redirect::route('allowances');


    }

    public function editAllowance(Setting $setting)
    {
        return View::make('settings.edit-allowance')->with('setting',
            $setting);
    }

    public function postEditAllowance(Setting $setting)
    {
        $setting->value = floatval(Input::get('value'));
        $setting->save();
        Session::flash(
            'success',
            'Allowance for ' . $setting->date->format('F Y') . ' has been
            saved.'
        );

        return Redirect::route('allowances');
    }

    public function deleteAllowance(Setting $setting)
    {
        return View::make('settings.delete-allowance')->with('setting',
            $setting);
    }

    public function postDeleteAllowance(Setting $setting)
    {
        $setting->delete();
        Session::flash(
            'success', $setting->date->format('F Y') . ' no longer
        has a specific allowance'
        );

        return Redirect::route('allowances');
    }

}