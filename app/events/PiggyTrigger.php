<?php

class PiggyTrigger
{

    public function validatePiggy(Piggybank $piggy)
    {
        $user = Auth::user();
        if(is_null(Auth::user())) {
            $user = User::find($piggy->user_id);
        }
        // find a similar component
        if (is_null($piggy->id)) {
            $piggies = $user->piggybanks()->get();
        } else {
            $piggies = $user->piggybanks()->where(
                'id', '!=', $piggy->id
            )->get();
        }
        foreach ($piggies as $dbp) {

            if ($piggy->name == $dbp->name) {
                return false;
            }
        }

        return true;
    }

    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Piggybank',
            'PiggyTrigger@validatePiggy'
        );

        $events->listen(
            'eloquent.updating: Piggybank',
            'PiggyTrigger@validatePiggy'
        );
    }

}
