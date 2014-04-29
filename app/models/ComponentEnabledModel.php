<?php


class ComponentEnabledModel extends Eloquent
{
    public function hasComponentOfType(Type $type)
    {
        $key = $this->id . '-' . $type->id . '-' . get_called_class();
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
        $key = $this->id . '-' . $type->id . '-' . get_called_class();
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

    public function saveComponentsFromInput()
    {
        foreach (Type::allTypes() as $type) {
            // split and get second part of Input:
            $input = Input::get($type->type);
            $parts = explode('/', $input);
            $name = isset($parts[1]) ? $parts[1] : $parts[0];

            $component = Component::firstOrCreate(
                ['name' => $name, 'type_id' => $type->id, 'user_id' => Auth::user()->id]
            );
            // if component is null, detach whatever component is on that spot, if any
            if (is_null($component->id) && $this->hasComponentOfType($type)) {
                $this->components()->detach($this->getComponentOfType($type));
            }

            // detach component of this type if different component is present
            if (!is_null($component->id) && $this->hasComponentOfType($type)
                && $this->getComponentOfType($type)->id != $component->id
            ) {
                $this->components()->detach($this->getComponentOfType($type));
            }
            // if has no component of this type, attach it:
            if (!is_null($component->id) && !$this->hasComponentOfType($type)) {
                $this->components()->attach($component);
            }
        }
    }

} 