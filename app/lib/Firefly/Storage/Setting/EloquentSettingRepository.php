<?php

namespace Firefly\Storage\Setting;


class EloquentSettingRepository implements SettingRepositoryInterface
{

    public function create($name)
    {
        return \Setting::create(
            [
                'user_id' => \Auth::user()->id,
                'type'    => 'string',
                'name'    => $name,
                'value'   => ''
            ]
        );
    }

    public function getSetting($name)
    {
        if (!\Auth::check()) {
            return null;
        }
        return \Auth::user()->settings()->whereName($name)->first();
    }

    public function getSettingByDate($name, \Carbon\Carbon $date)
    {
        // TODO: Implement getSettingByDate() method.
        die('Not yet implemented.');
    }

    public function getSettingByAccount($name, \Account $account)
    {
        // TODO: Implement getSettingByAccount() method.
        die('Not yet implemented.');
    }

    public function getSettingByAccountAndDate($name, \Account $account, \Carbon\Carbon $date)
    {
        // TODO: Implement getSettingByAccountAndDate() method.
        die('Not yet implemented.');
    }

    public function getSettingValue($name)
    {
        if (!\Auth::check()) {
            return null;
        }
        $setting = \Auth::user()->settings()->whereName($name)->first();
        if ($setting) {
            return $setting->value;
        } else {
            // no more default settings bullshit.
            return null;
        }
    }

    public function getSettingValueByDate($name, \Carbon\Carbon $date)
    {
        if (!\Auth::check()) {
            return null;
        }
        $setting = \Auth::user()->settings()->whereName($name)->whereDate($date->format('Y-m-d'))->first();
        if ($setting) {
            return $setting->value;
        } else {
            // no more default settings bullshit.
            return null;
        }

    }

    public function getSettingValueByAccount($name, \Account $account)
    {
        // TODO: Implement getSettingValueByAccount() method.
        die('Not yet implemented.');
    }

    public function getSettingValueByAccountAndDate($name, \Account $account, \Carbon\Carbon $date)
    {
        if (!\Auth::check()) {
            return null;
        }
        $setting = \Auth::user()->settings()->whereAccountId($account->id)->whereName($name)->whereDate($date->format('Y-m-d'))->first();
        if ($setting) {
            return $setting->value;
        } else {
            // no more default settings bullshit.
            return null;
        }
    }

    public function getDefaultSetting($name)
    {
        // TODO: Implement getDefaultSetting() method.
        die('Not yet implemented.');
    }
}