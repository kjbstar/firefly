<?php


/**
 * Type
 *
 * @property integer                                                    $id
 * @property \Carbon\Carbon                                             $created_at
 * @property \Carbon\Carbon                                             $updated_at
 * @property string                                                     $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 */
class Type extends Eloquent
{
    public static $rules
        = [
            'type' => 'required|between:1,40',
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['type'];

    public function components()
    {
        return $this->hasMany('Component');
    }

    public function getIcon()
    {
        switch ($this->type) {
            case 'beneficiary':
                return 'i/user_gray.png';
                break;
            case 'category':
                return 'i/tag_blue.png';
                break;
            case 'budget':
                return 'i/money.png';
                break;
            case 'payer':
                return 'i/user_go.png';
                break;
        }
        return 'i/error.png';
    }

    public static function allTypes()
    {
        if (Cache::has('types')) {
            return Cache::get('types');
        } else {
            $types = Type::orderBy('type')->get();
            Cache::forever('types', $types);
            return $types;
        }
    }

} 