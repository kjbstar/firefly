<?php
use Carbon\Carbon as Carbon;

class SearchHelper
{
    public static function parseQuery()
    {
        $data = [
            'originalQuery' => Input::get('query'),
            'specials' => [
                'afterDate'  => false,
                'beforeDate' => false,
            ],
            'queryParts' => [],
            'query' => ''
        ];
        // explode:
        $parts = explode(' ', Input::get('query'));

        foreach ($parts as $part) {
            if (!(strpos($part, ':') === false)) {
                // special search item!
                $specialParts = explode(':', $part);
                switch ($specialParts[0]) {
                    case 'after':
                        $data['specials']['afterDate'] = new Carbon($specialParts[1]);
                        break;
                    case 'before':
                        $data['specials']['beforeDate'] = new Carbon($specialParts[1]);
                        break;
                }
            } else {
                $data['queryParts'][] = $part;
            }
        }
        $data['query'] = '%'.join('%',$data['queryParts']).'%';
        $data['queryText'] = join(' ',$data['queryParts']);
        $data['md5'] = md5(print_r($data,true));
        return $data;
    }

    public static function searchTransactions($search)
    {
        $query = Auth::user()->transactions()->where('description', 'LIKE', $search['query']);
        $result = [
            'count'  => 0,
            'result' => null
        ];
        if ($search['specials']['afterDate']) {
            $query->afterDate($search['specials']['afterDate']);
        }
        if ($search['specials']['beforeDate']) {
            $query->beforeDate($search['specials']['afterDate']);
        }
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchTransfers($search)
    {
        $query = Auth::user()->transfers()->where('description', 'LIKE', $search['query']);
        $result = [
            'count'  => 0,
            'result' => null
        ];
        if ($search['specials']['afterDate']) {
            $query->afterDate($search['specials']['afterDate']);
        }
        if ($search['specials']['beforeDate']) {
            $query->beforeDate($search['specials']['afterDate']);
        }
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchAccounts($search)
    {
        $result = [
            'count'  => 0,
            'result' => null
        ];
        $query = Auth::user()->accounts()->where('name', 'LIKE', $search['query']);
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;
    }

    public static function searchBeneficiaries($search)
    {
        return self::_searchComponents('beneficiary', $search['query']);
    }

    public static function searchCategories($search)
    {
        return self::_searchComponents('category', $search['query']);
    }

    public static function searchBudgets($search)
    {
        return self::_searchComponents('budget', $search['query']);
    }

    /**
     * @param $type
     * @param $searchQuery
     *
     * @return array
     */
    private static function _searchComponents($type, $search)
    {
        $type = Type::whereType($type)->first();
        $result = [
            'count'  => 0,
            'result' => null
        ];
        $query = Auth::user()->components()->where('type_id', $type->id)->where('name', 'LIKE', $search);
        $result['count'] = $query->count();
        if ($result['count'] <= 20) {
            $result['result'] = $query->get();
        }
        return $result;

    }
}