<?php

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');

use Carbon\Carbon as Carbon;

class ReportController extends BaseController
{
    public function index()
    {
        $first = Toolkit::getEarliestEvent();
        $today = new Carbon;
        $first->startOfYear();
        $years = [];
        while ($first <= $today) {
            $years[] = $first->format('Y');
            $first->addYear();
        }


        return View::make('reports.index')->with('title', 'Reports')->with(
            'years', $years
        );
    }
}