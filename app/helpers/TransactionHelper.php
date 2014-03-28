<?php


class TransactionHelper
{
    public static function emptyPrefilledAray()
    {
        return [
            'description'      => '',
            'amount'           => '',
            'date'             => date('Y-m-d'),
            'account_id'       => null,
            'beneficiary'      => '',
            'category'         => '',
            'budget'           => '',
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
    }

    public static function prefilledFromPredictable(Predictable $predictable)
    {
        $dayOfMonth = sprintf('%02d', $predictable->dom);
        return [
            'description'      => $predictable->description,
            'amount'           => $predictable->amount,
            'date'             => date('Y-m-') . $dayOfMonth,
            'account_id'       => null,
            'beneficiary'      => is_null($predictable->beneficiary) ? '' : $predictable->beneficiary->name,
            'category'         => is_null($predictable->category) ? '' : $predictable->category->name,
            'budget'           => is_null($predictable->budget) ? '' : $predictable->budget->name,
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
    }

    public static function prefilledFromOldInput()
    {
        return ['description'      => Input::old('description'),
                'amount'           => floatval(Input::old('amount')),
                'date'             => Input::old('date'),
                'account_id'       => intval(Input::old('account_id')),
                'beneficiary'      => intval(Input::old('beneficiary_id')),
                'category'         => intval(Input::old('category_id')),
                'budget'           => intval(Input::old('budget_id')),
                'ignoreprediction' => intval(Input::old('ignoreprediction')),
                'ignoreallowance'  => intval(Input::old('ignoreallowance')),
                'mark'             => intval(Input::old('mark'))

        ];
    }

    public static function saveComponentFromText($type, $name)
    {
        $parts = explode('/', $name);
        if (count($parts) > 2) {
            Session::flash('error', 'Could not save ' . $type . ' "' . htmlentities($name) . '" due to errors.');
            return null;
        }

        if (count($parts) == 1) {
            return Component::findOrCreate($type, $name);
        }
        if (count($parts) == 2) {
            $parent = Component::findOrCreate($type, $parts[0]);
            $object = Component::findOrCreate($type, $parts[1]);
            $object->parent_component_id = $parent->id;
            $object->save();
            return $object;
        }
        return null;

    }
}