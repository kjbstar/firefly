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
        return \Auth::user()->settings()->whereName($name)->first();
    }

    public function getSettingByDate($name, Carbon $date)
    {
        // TODO: Implement getSettingByDate() method.
        die('Not yet implemented.');
    }

    public function getSettingByAccount($name, Account $account)
    {
        // TODO: Implement getSettingByAccount() method.
        die('Not yet implemented.');
    }

    public function getSettingByAccountAndDate($name, Account $account, Carbon $date)
    {
        // TODO: Implement getSettingByAccountAndDate() method.
        die('Not yet implemented.');
    }

    public function getSettingValue($name)
    {
        $setting = \Auth::user()->settings()->whereName($name)->first();
        if ($setting) {
            return $setting->value;
        } else {
            // no more default settings bullshit.
            return null;
        }
    }

    public function getSettingValueByDate($name, Carbon $date)
    {
        // TODO: Implement getSettingValueByDate() method.
        die('Not yet implemented.');
    }

    public function getSettingValueByAccount($name, Account $account)
    {
        // TODO: Implement getSettingValueByAccount() method.
        die('Not yet implemented.');
    }

    public function getSettingValueByAccountAndDate($name, Account $account, Carbon $date)
    {
        // TODO: Implement getSettingValueByAccountAndDate() method.
        die('Not yet implemented.');
    }

    public function getDefaultSetting($name)
    {
        // TODO: Implement getDefaultSetting() method.
        die('Not yet implemented.');
    }
}