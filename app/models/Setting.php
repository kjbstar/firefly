<?php
use Carbon\Carbon as Carbon;

/**
 * Class Setting
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $user_id
 * @property string         $type
 * @property string         $name
 * @property string         $date
 * @property string         $value
 * @property-read \User     $user
 * @method static \Illuminate\Database\Query\Builder|\Setting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\Setting whereValue($value)
 */
class Setting extends Eloquent
{
    public static $rules
        = ['name'    => 'required|between:1,500',
           'user_id' => 'required|exists:users,id',
           'type'    => 'in:date,float,string,int', 'value' => 'required'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['user_id', 'name', 'date','type', 'value'];

    public static function findSetting($name)
    {
        return Auth::user()->settings()->where(
            'name', $name
        )->first();

    }

    /**
     * Return a setting by name.
     *
     * @param $name
     *
     * @return Setting
     */
    public static function getSetting($name)
    {
        $key = Auth::user()->id.'setting'.$name;
        if(Cache::has($key)) {
            return Cache::get($key);
        } else {
        $userSetting = Auth::user()->settings()->where('name', $name)->first();

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
        Cache::put($key,$userSetting,2880);
        return $userSetting;
        }
    }

    /**
     * Gets the description as a decrypted string.
     *
     * TODO expand into other types.
     *
     * @param $value
     *
     * @return null|string
     */
    public function getValueAttribute($value)
    {
        $type = isset($this->attributes['type']) ? $this->attributes['type'] : 'default';
        $return = null;
        switch ($type) {
            case 'date':
                $return = new Carbon($value);
                break;
            case 'float':
                $return = floatval($value);
                break;
            case 'int':
                $return = intval($value);
                break;
            case 'string':
                $return = trim($value);
                break;
        }
        return $return;
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