<?php

class EncryptionValidator extends Illuminate\Validation\Validator
{

    public function validateAccountName($attribute, $value, $parameters)
    {
        $accounts = Auth::user()->accounts()->get();
        foreach ($accounts as $account) {
            if ($account->name == $value) {
                return false;
            }
        }

        return true;
    }

    public function validateComponentName($attribute, $value, $parameters)
    {
        $components = Auth::user()->components()->get();
        foreach ($components as $component) {
            if ($component->name == $value) {
                return false;
            }
        }

        return true;
    }

    public function validatePredictableDescription($attribute, $value, $parameters)
    {
        $predictables = Auth::user()->predictables()->get();
        foreach ($predictables as $predictable) {
            if ($predictable->description == $value) {
                return false;
            }
        }

        return true;
    }
}

Validator::resolver(function($translator, $data, $rules, $messages)
    {
        return new EncryptionValidator($translator, $data, $rules, $messages);
    });
