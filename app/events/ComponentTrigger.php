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

        // also run the validator again.
        $validator = Validator::make($component->toArray(), Component::$rules);
        if ($validator->fails()) {
            Session::flash('error_extended', $validator->messages()->first());
            Log::error('Validator failed while making component: ' . print_r($validator->messages()->all(), true));
            return false;
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
                Session::flash(
                    'error_extended',
                    'There is already a ' . $component->type->type . ' with the name "' . $component->name . '".'
                );
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
            Session::flash('error_extended', 'Cannot attach component to itself!');
            Log::debug('Found a parent_component problem.');
            return false;
        }
        // component cannot be attached to a component which already
        // is a child:
        if (!is_null($component->parent_component_id)) {
            $parent = $user->components()->find($component->parent_component_id);
            // parent must be valid parent:
            if (is_null($parent)) {
                Session::flash('error_extended', 'Cannot attach to this component, does not exist.');
                Log::debug('Invalid parent.');
                return false;
            }
            if (!is_null($parent->parent_component_id)) {
                Session::flash('error_extended', 'Parent is a child, cannot nest connections.');
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
        $events->listen('eloquent.creating: Component', 'ComponentTrigger@validateComponent');
        $events->listen('eloquent.updating: Component', 'ComponentTrigger@validateComponent');
    }

}
