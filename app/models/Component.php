<?php
use Carbon\Carbon as Carbon;
/**
 * Class Component
 */
class Component extends Eloquent
{
    public static $rules
        = ['name'                => 'required|between:1,500',
           'user_id'             => 'required|exists:users,id',
           'parent_component_id' => 'exists:components,id',];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['name', 'user_id', 'parent_component_id', 'type'];

    /**
     * This method either finds a component by the name $name or creates it
     * for the current user.
     *
     * @param string $type The type of component.
     * @param string $name The name of the new component.
     *
     * @return Component|null
     */
    public static function findOrCreate($type, $name)
    {
        if (strlen($name) == 0) {
            return null;
        }

        $strpos = strpos($name, '/');
        if (!($strpos === false)) {
            $name = substr($name, ($strpos + 1));
        }

        $componentID = null;
        if (Auth::check()) {
            foreach (
                Auth::user()->components()->where('type', $type)->get() as $c
            ) {
                if ($c->name == $name) {
                    return $c;
                }
            }
            if (is_null($componentID)) {
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

    public function predictForMonth(Carbon $date) {
        $predictionStart = Setting::getSetting('predictionStart');
        $start = new Carbon($predictionStart->value);
        $date->subMonth();
        $count = 0;
        $sum = 0;
        while($start <= $date) {
            $current = clone $start;
            $sum += floatval($this->transactions()->inMonth($current)
                    ->where('ignoreprediction',0)->expenses()->sum('amount')
                *-1);
            $start->addMonth();
            $count++;
        }
        if($count > 1) {
            return $sum / $count;
        } else {
            return $sum;
        }
    }

    /**
     * Gets the parent component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentComponent()
    {
        return $this->belongsTo('Component');
    }

    /**
     * Get all child components.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childrenComponents()
    {
        return $this->hasMany('Component', 'parent_component_id');
    }

    /**
     * Get all limits for this component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function limits()
    {
        return $this->hasMany('Limit');
    }

    /**
     * Get the transactions belonging to this component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function transactions()
    {
        return $this->belongsToMany('Transaction');
    }

    /**
     * Return the user this Component belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Get the component name decrypted.
     *
     * @param $value
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Encrypt the name while setting it.
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
    }

    /**
     * All date fields that need to be converted to Carbon objects (and back).
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'deleted_at'];
    }


} 