<?php

class ComponentTrigger
{

    public function validateComponent(Component $component)
    {
        // find a similar component
        if (is_null($component->id)) {
            $components = Auth::user()->components()->where(
                'type', $component->type
            )->get();
        } else {
            $components = Auth::user()->components()->where(
                'type', $component->type
            )->where(
                'id', '!=', $component->id
            )->get();
        }
         foreach ($components as $dbc) {

            if ($component->name == $dbc->name) {
                return false;
            }
        }
        // component cannot be attached to itself:
        // but both can be NULL
        if ($component->parent_component_id == $component->id
            && !is_null($component->parent_component_id)
            && !is_null($component->id)
        ) {

            return false;
        }
        // component cannot be attached to a component which already
        // is a child:
        if (!is_null($component->parent_component_id)) {
            $parent = Auth::user()->components()->find(
                $component->parent_component_id
            );
            // parent must be valid parent:
            if (is_null($parent)) {
                return false;
            }
            if (!is_null($parent->parent_component_id)) {
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

$subscriber = new ComponentTrigger;
Event::subscribe($subscriber);
