<?php

/**
 * Class ComponentTrigger
 */
class ComponentTrigger
{

    /**
     * @param Component $component
     *
     * @return bool
     */
    public function validateComponent(Component $component)
    {
        $user = Auth::user();
        if (is_null(Auth::user())) {
            $user = User::find($component->user_id);
        }

        // find a similar component
        if (is_null($component->id)) {

            $components = $user->components()->where('type_id', $component->type_id)->get();
        } else {
            $components = $user->components()->where('type_id', $component->type_id)->where('id', '!=', $component->id)
                ->get();
        }
        foreach ($components as $dbc) {
            if ($component->name == $dbc->name) {
                Log::debug('Found a duplicate component: ' . $component->name . ' matches existing ' . $dbc->name);
                return false;
            }
        }
        // component cannot be attached to itself:
        // but both can be NULL
        if ($component->parent_component_id == $component->id && !is_null($component->parent_component_id)
            && !is_null(
                $component->id
            )
        ) {
            Log::debug('Found a parent_component problem.');
            return false;
        }
        // component cannot be attached to a component which already
        // is a child:
        if (!is_null($component->parent_component_id)) {
            $parent = $user->components()->find(
                $component->parent_component_id
            );
            // parent must be valid parent:
            if (is_null($parent)) {
                Log::debug('Invalid parent.');
                return false;
            }
            if (!is_null($parent->parent_component_id)) {
                Log::debug('Parent already a parent.');
                return false;
            }
        }

        return true;
    }

    /**
     * Make the triggers.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Component', 'ComponentTrigger@validateComponent'
        );

        $events->listen(
            'eloquent.updating: Component', 'ComponentTrigger@validateComponent'
        );
    }

}
