<?php
use Carbon\Carbon as Carbon;
/**
 * Class Setting
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property string $type
 * @property string $name
 * @property string $date
 * @property string $value
 * @property-read \User $user
 */
class Setting extends Eloquent
{
    public static $rules
        = ['name'    => 'required|between:1,500',
           'user_id' => 'required|exists:users,id', 'type' => 'in:date,float,string,int',
           'value'   => 'required'];
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
                /** @noinspection PhpParamsInspection */
                $userSetting->user()->associate(Auth::user());
                $userSetting->save();
            }
        }

        return $userSetting;
    }

    /**
     * Gets the description as a decrypted string.
     *
     * @param $value
     *
     * @return null|string
     */
    public function getValueAttribute($value)
    {
        if(isset($this->attributes['type']) && $this->attributes['type'] == 'date') {
            return new Carbon($value);
        } else {
            return $value;
        }

    }

    /**
     * Set the description as an encrypted string.
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value;
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

    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }


}