<?php


class SearchHelper
{
    public static function searchTransactions($searchQuery, $specials)
    {
        $query = Auth::user()->transactions()->where('description', 'LIKE', $searchQuery);
        $result = [
            'count'  => 0,
            'result' => null
        ];
        if ($specials['afterDate']) {
            $query->afterDate($specials['afterDate']);
        }
        if ($specials['beforeDate']) {
            $query->beforeDate($specials['afterDate']);
        }
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchTransfers($searchQuery, $specials)
    {
        $query = Auth::user()->transfers()->where('description', 'LIKE', $searchQuery);
        $result = [
            'count'  => 0,
            'result' => null
        ];
        if ($specials['afterDate']) {
            $query->afterDate($specials['afterDate']);
        }
        if ($specials['beforeDate']) {
            $query->beforeDate($specials['afterDate']);
        }
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchAccounts($searchQuery, $specials)
    {
        $result = [
            'count'  => 0,
            'result' => null
        ];
        $query = Auth::user()->accounts()->where('name', 'LIKE', $searchQuery);
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchBeneficiaries($searchQuery, $specials)
    {
        return self::_searchComponents('beneficiary', $searchQuery);
    }

    public static function searchCategories($searchQuery, $specials)
    {
        return self::_searchComponents('category', $searchQuery);
    }

    public static function searchBudgets($searchQuery, $specials)
    {
        return self::_searchComponents('budget', $searchQuery);
    }

    /**
     * @param $type
     * @param $searchQuery
     *
     * @return array
     */
    private static function _searchComponents($type, $searchQuery)
    {
        $type = Type::whereType($type)->first();
        $result = [
            'count'  => 0,
            'result' => null
        ];
        $query = Auth::user()->components()->where('type_id', $type->id)->where('name', 'LIKE', $searchQuery);
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;

    }

    ////                $plur = Str::plural($component);
//                $q = Auth::user()->components()->where('type', $component)->where('name', 'LIKE', $sql);
//                $counts[$plur] = $q->count();
//                if ($counts[$plur] <= 20) {
//                    $results[$plur] = $q->get();
//                }
//                unset($q);

} 