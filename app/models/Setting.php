<?php
use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Setting
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $user_id
 * @property integer        $account_id
 * @property string         $type
 * @property string         $name
 * @property \Carbon\Carbon $date
 * @property string         $value
 * @property-read \Account  $account
 * @property-read \User     $user
 */
class Setting extends Eloquent
{
    public static $rules
        = [
            'name'       => 'required|between:1,50',
            'user_id'    => 'required|exists:users,id',
            'type'       => 'in:date,float,string,int',
            'value'      => 'required',
            'account_id' => 'exists:accounts,id'
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['user_id', 'name', 'date', 'type', 'value', 'account_id'];

    /**
     * @param $name
     * @deprecated Will be removed.
     *
     * @return mixed
     */
    public static function findSetting($name)
    {
        return Auth::user()->settings()->where('name', $name)->first();

    }

    /**
     * Which account does this setting belong to?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account');
    }

    /**
     * Return a setting by name.
     *
     * @param $name
     * @deprecated Since v2.1.4
     *
     * @return Setting
     */
    public static function getSetting($name)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $key = 'setting' . $name;
        if (Cache::has($key)) {
            // @codeCoverageIgnoreStart
            return Cache::get($key);
            // @codeCoverageIgnoreEnd
        } else {
            // user might not be logged in!
            if (!Auth::user()) {
                $configInfo = Config::get('firefly.' . $name);
                $userSetting = new Setting;
                $userSetting->name = $name;
                $userSetting->account_id = null;
                $userSetting->type = $configInfo['type'];
                $userSetting->value = $configInfo['value'];
                /** @noinspection PhpParamsInspection */
                return $userSetting;
            }
            $userSetting = Auth::user()->settings()->where('name', $name)->first();

            if (is_null($userSetting)) {
                // create a new setting with the default
                // value from a config file:
                $configInfo = Config::get('firefly.' . $name);
                if (!is_null($configInfo)) {
                    $userSetting = new Setting;
                    $userSetting->name = $name;
                    $userSetting->account_id = null;
                    $userSetting->type = $configInfo['type'];
                    $userSetting->value = $configInfo['value'];
                    /** @noinspection PhpParamsInspection */
                    $userSetting->user()->associate(Auth::user());
                    $userSetting->save();
                }
            }
            Cache::put($key, $userSetting, 2880);
            return $userSetting;
        }
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

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }


}