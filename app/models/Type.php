<?php


/**
 * Type
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @method static \Illuminate\Database\Query\Builder|\Type whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Type whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Type whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Type whereType($value) 
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
} 