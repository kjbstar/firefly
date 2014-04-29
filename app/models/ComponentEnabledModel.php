<?php


class ComponentEnabledModel extends Eloquent
{
    public function hasComponentOfType(Type $type)
    {
        $key = $this->id . '-' . $type->id . '-'.get_called_class();
        if (Cache::has($key)) {
            return true;
        } else {
            // find it, if has it, cache it, return result.
            foreach ($this->components as $component) {
                if ($component->type_id == $type->id) {
                    Cache::forever($key, $component);
                    return true;
                }
            }
        }
        return false;
    }

    public function getComponentOfType(Type $type)
    {
        $key = $this->id . '-' . $type->id . '-'.get_called_class();
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            // find it, cache it, return it:
            foreach ($this->components as $component) {
                if ($component->type_id == $type->id) {
                    Cache::forever($key, $component);
                    return $component;
                }
            }
        }
        return null;
    }

} 