<?php
/**
 * An Eloquent Model: 'Component'
 *
 * @property integer                                                      $id
 * @property \Carbon\Carbon                                               $created_at
 * @property \Carbon\Carbon                                               $updated_at
 * @property \Carbon\Carbon                                               $deleted_at
 * @property integer                                                      $parent_component_id
 * @property string                                                       $type
 * @property string                                                       $name
 * @property integer                                                      $user_id
 * @property-read \Component                                              $parentComponent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[]   $childrenComponents
 * @property-read \Illuminate\Database\Eloquent\Collection|\Limit[]       $limits
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @property-read \User                                                   $user
 */
class Component extends Eloquent
{
    public static $rules
        = ['name'                => 'required|between:1,500',
                'user_id'             => 'required|exists:users,id',
                'parent_component_id' => 'exists:components,id',];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['name', 'user_id', 'parent_component_id', 'type'];

    public static function findOrCreate($type, $name)
    {
        if (strlen($name) == 0) {
            return null;
        }

        $strpos = strpos($name, '/');
        if (!($strpos === false)) {
            $name = substr($name, ($strpos + 1));
        }

        $id = null;
        if (Auth::check()) {
            foreach (
                Auth::user()->components()->where('type', $type)->get() as $c
            ) {
                if ($c->name == $name) {
                    return $c;
                }
            }
            if (is_null($id)) {
                $component = new Component(['name'   => $name,
                                           'user_id' => Auth::user()->id,
                                           'type'    => $type]);
                $component->save();
                if (isset($component->id)) {
                    return $component;
                }
            }
        }

        return null;
    }

    public function parentComponent()
    {
        return $this->belongsTo('Component');
    }

    public function childrenComponents()
    {
        return $this->hasMany('Component', 'parent_component_id');
    }

    public function limits()
    {
        return $this->hasMany('Limit');
    }

    public function transactions()
    {
        return $this->belongsToMany('Transaction');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
    }
    public function getDates()
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }


} 