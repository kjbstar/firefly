<?php

use Carbon\Carbon as Carbon;

/**
 * Class SearchController
 */
class SearchController extends BaseController
{

    private $cacheEnabled = true;

    /**
     * Search
     *
     * @return \Illuminate\View\View
     */
    public function search()
    {
        // special search things:
        $search = SearchHelper::parseQuery();

        // check cache:
        $key = 'search-' . $search['md5'];

        if (Cache::has($key) && $this->cacheEnabled
        ) {
            // @codeCoverageIgnoreStart
            $result = Cache::get($key);
        } else {
            // @codeCoverageIgnoreEnd
            $result = [
                'time'   => new Carbon,
                'result' => [],
                'count'  => [],
            ];

            // search for these models:
            $models = ['transactions', 'transfers', 'accounts', 'beneficiaries', 'budgets', 'categories'];
            foreach ($models as $model) {
                $methodName = 'search' . ucfirst($model);
                $current = SearchHelper::$methodName($search);
                $result['result'][$model] = $current['result'];
                $result['count'][$model] = $current['count'];
            }
            Cache::forever($key, $result);
            unset($result['time']);
        }

        return View::make('home.search')->with('search', $search)->with('result', $result)->with(
            'title', 'Searching for "' . $search['originalQuery'] . '"'
        );
    }
}