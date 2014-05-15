<?php

/**
 * Class PiggyTrigger
 */
class PiggyTrigger
{

    /**
     * @param Piggybank $piggy
     *
     * @return bool
     */
    public function validatePiggy(Piggybank $piggy)
    {
        $user = Auth::user();
        if (is_null(Auth::user())) {
            $user = User::find($piggy->user_id);
        }
        // find a similar component
        if (is_null($piggy->id)) {
            $piggies = $user->piggybanks()->get();
        } else {
            $piggies = $user->piggybanks()->where('id', '!=', $piggy->id)->get();
        }
        foreach ($piggies as $dbp) {

            if ($piggy->name == $dbp->name) {
                Session::flash('error_extended','Piggy bank with name "'.$piggy->name.'" already exists.');
                Log::error('Piggy trigger: ' . $piggy->name.' exists already (#'.$dbp->id.')');
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen('eloquent.creating: Piggybank','PiggyTrigger@validatePiggy');
        $events->listen('eloquent.updating: Piggybank','PiggyTrigger@validatePiggy');
    }

}
