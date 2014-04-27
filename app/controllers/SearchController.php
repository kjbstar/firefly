<?php
use Carbon\Carbon as Carbon;
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/SearchHelper.php');

class SearchController extends BaseController
{

    private $_cacheEnabled = true;
    public function search()
    {
        // grab query:
        $query = Input::get('query');
        $originalQuery = $query;
        // explode query:
        $parts = explode(' ', $query);
        $terms = [];

        // special search things:
        $specials = [
            'afterDate'  => false,
            'beforeDate' => false,
        ];
        foreach ($parts as $part) {
            if (!(strpos($part, ':') === false)) {
                // special search item!
                $specialParts = explode(':', $part);
                switch ($specialParts[0]) {
                    case 'after':
                        $specials['afterDate'] = new Carbon($specialParts[1]);
                        break;
                    case 'before':
                        $specials['beforeDate'] = new Carbon($specialParts[1]);
                        break;
                }
            } else {
                $terms[] = $part;
            }
        }

        $query = join(' ', $terms);
        $sql = '%' . str_replace(' ', '%', $query) . '%';
        $md5 = md5($sql . print_r($specials,true));
        // have searched for this before:
        if (
            Cache::has('search-time-' . $md5)
            && Cache::has('search-results-' . $md5)
            && Cache::has('search-counts-' . $md5)
            && $this->_cacheEnabled
        ) {
            $results = Cache::get('search-results-' . $md5);
            $counts = Cache::get('search-counts-' . $md5);
            $time = Cache::get('search-time-' . $md5);
        } else {
            Cache::forever('search-time-' . $md5, new Carbon);
            $time = null;
            $results = [];
            $counts = [];

            // search for these models:
            $models = ['transactions','transfers','accounts','beneficiaries','budgets','categories'];
            foreach($models as $model) {
                $methodName = 'search'.ucfirst($model);
                $result = SearchHelper::$methodName($sql,$specials);
                $results[$model] = $result['result'];
                $counts[$model] = $result['count'];
            }
            Cache::forever('search-results-' . $md5, $results);
            Cache::forever('search-counts-' . $md5, $counts);
        }

        return View::make('home.search')->with('query', $originalQuery)->with('queryText',$query)->with('results', $results)->with('counts', $counts)
            ->with('time', $time)->with('specials',$specials);
    }
}