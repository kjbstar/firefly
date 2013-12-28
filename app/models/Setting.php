<?php

/**
 * Class Setting
 */
class Setting extends Eloquent
{
    public static $rules
        = ['name'    => 'required|between:1,500',
           'user_id' => 'required|exists:users,id', 'type' => 'in:date',
            'value' => 'required'];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['user_id', 'name', 'type', 'value'];

    /**
     * Return a setting by name.
     *
     * @param $name
     *
     * @return Setting
     */
    public static function getSetting($name)
    {
        $userSetting = Auth::user()->settings()->where(
            'name', $name
        )->first();
        if (is_null($userSetting)) {
            // create a new setting with the default
            // value from a config file:
            $configInfo = Config::get('firefly.' . $name);
            if (!is_null($configInfo)) {
                $userSetting = new Setting;
                $userSetting->name = $name;
                $userSetting->type = $configInfo['type'];
                $userSetting->value = $configInfo['value'];
                $userSetting->user()->associate(Auth::user());
                $userSetting->save();
            }
        }

        return $userSetting;
    }

    /**
     * Setting belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }


}