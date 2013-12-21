<?php

/**
 * Class SettingsController
 */
class SettingsController extends BaseController {

    public function index() {
        return View::make('settings.index')->with('title','Settings');
    }
} 