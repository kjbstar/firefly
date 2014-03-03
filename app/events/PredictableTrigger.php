<?php

class PredictableTrigger
{

    public function validatePredictable(Predictable $predictable)
    {
        // find a similar component
        if (is_null($predictable->id)) {
            $predictables = Auth::user()->predictables()->get();
        } else {
            $predictables = Auth::user()->predictables()->where(
                'id', '!=', $predictable->id
            )->get();
        }
        foreach ($predictables as $dbp) {

            if ($predictable->description == $dbp->description) {
                return false;
            }
        }

        return true;
    }

    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Predictable',
            'PredictableTrigger@validatePredictable'
        );

        $events->listen(
            'eloquent.updating: Predictable',
            'PredictableTrigger@validatePredictable'
        );
    }

}
