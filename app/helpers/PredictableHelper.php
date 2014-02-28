<?php

class PredictableHelper
{
    public static function componentList()
    {
        $list = ['beneficiary' => [0 => '(none)'], 'budget' => [0 => '(none)'],
                 'category'    => [0 => '(none)']];
        $components = Auth::user()->components()->get();
        foreach ($components as $component) {
            $t = $component->type;
            $id = $component->id;
            $name = $component->name;

            // is a parent:
            if ($component->childrenComponents()->count() > 0) {
                $list[$t][$name] = isset($list[$t][$name]) ? $list[$t][$name]
                    : [];

            }

            // is a child:
            if (!is_null($component->parent_component_id)) {
                $parentName = $component->parentComponent()->first()->name;
                $list[$t][$parentName][$id] = $name;
            }
            // neither:
            if (is_null($component->parent_component_id)
                && $component->childrenComponents()->count() == 0
            ) {
                $list[$t][$id] = $name;
            }
        }

        return $list;
    }

}