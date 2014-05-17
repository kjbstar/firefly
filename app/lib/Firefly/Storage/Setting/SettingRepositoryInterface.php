<?php

namespace Firefly\Storage\Setting;


interface SettingRepositoryInterface
{
    public function create($name);

    public function getSetting($name);

    public function getSettingByDate($name, Carbon $date);

    public function getSettingByAccount($name, Account $account);

    public function getSettingByAccountAndDate($name, Account $account, Carbon $date);

    public function getSettingValue($name);

    public function getSettingValueByDate($name, Carbon $date);

    public function getSettingValueByAccount($name, Account $account);

    public function getSettingValueByAccountAndDate($name, Account $account, Carbon $date);

    public function getDefaultSetting($name);
}