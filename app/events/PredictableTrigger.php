<?php

/**
 * Class PredictableTrigger
 */
class PredictableTrigger
{

    /**
     * @param Predictable $predictable
     *
     * @return bool
     */
    public function validatePredictable(Predictable $predictable)
    {
        $user = Auth::user();
        if (is_null(Auth::user())) {
            $user = User::find($predictable->user_id);
        }
        // find a similar component
        if (is_null($predictable->id)) {
            $predictables = $user->predictables()->get();
        } else {
            $predictables = $user->predictables()->where(
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

    /**
     * @param Predictable $predictable
     */
    public function jobPredictable(Predictable $predictable)
    {
        Log::debug('Trigger new predictable scan.');
        Queue::push('PredictableQueue@scan', ['predictable_id' => $predictable->id]);
    }

    /**
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Predictable',
            'PredictableTrigger@validatePredictable'
        );
        $events->listen('eloquent.saved: Predictable', 'PredictableTrigger@jobPredictable');

        $events->listen(
            'eloquent.updating: Predictable',
            'PredictableTrigger@validatePredictable'
        );
    }

}
