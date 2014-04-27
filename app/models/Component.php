<?php

/**
 * Class Component
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Predictable[] $predictables
 * @property boolean                                                      $reporting
 * @method static Component reporting()
 * @method static \Illuminate\Database\Query\Builder|\Component whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereParentComponentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Component whereReporting($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]    $transfers
 * @property integer                                                      $type_id
 * @method static \Illuminate\Database\Query\Builder|\Component whereTypeId($value)
 */
class Component extends Eloquent
{
    public static $rules
        = ['name'                => 'required|between:1,500',
           'user_id'             => 'required|exists:users,id',
           'reporting'           => 'numeric|between:0,1',
           'parent_component_id' => 'exists:components,id',
           'mime'                => 'between:0,50'
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['reporting', 'name', 'user_id', 'type_id','parent_component_id'];

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
        $user = Auth::user();
        if (is_null($user)) {
            return null;
        }

        foreach (
            $user->components()->where('type', $type)->get() as $c
        ) {
            if ($c->name == $name) {
                return $c;
            }
        }
        /** @noinspection PhpUndefinedFieldInspection */
        $component = new Component(['name'    => $name,
                                    'user_id' => Auth::user()->id,
                                    'type'    => $type]);
        $component->save();
        return $component;
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
     * Get the type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {

        return $this->belongsTo('Type');
    }

    /**
     * Get the type
     *
     * @return Type|null
     */
    public function getTypeAttribute()
    {

        $key = $this->id . '-component-type';
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        $type = $this->type()->first();
        Cache::forever($key, $type);
        return $type;
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
     * Get the transfers belonging to this component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function transfers()
    {
        return $this->belongsToMany('Transfer');
    }

    /**
     * Get the predictables belonging to this component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function predictables()
    {
        return $this->belongsToMany('Predictable');
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
     * All date fields that need to be converted to Carbon objects (and back).
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at'];
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeReporting($query)
    {
        return $query->where('reporting', 1);
    }

    /**
     * Get the destination where the icon will be stored.
     *
     * @return string
     */
    public static function getDestinationPath()
    {
        return app_path('storage') . DIRECTORY_SEPARATOR . 'components';
    }

    /**
     * Check if there is an icon present at the moment.
     */
    public function hasIcon()
    {
        if ($this->id) {
            $file = $this->iconFileLocation();
            return file_exists($file);
        }
        return false;
    }

    /**
     * Returns the full <img> URL when there is an image present.
     * Otherwise, returns "".
     */
    public function iconTag()
    {
        if ($this->hasIcon()) {
            return '<img src="' . URL::Route('componenticon', $this->id) . '" alt="' . htmlentities($this->name)
            . '" title="' . htmlentities($this->name) . '" />';
        }
        return '';
    }

    public function iconFileLocation()
    {
        return self::getDestinationPath() . DIRECTORY_SEPARATOR . $this->id . '.png';
    }


} 