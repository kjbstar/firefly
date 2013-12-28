<?php

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
} 