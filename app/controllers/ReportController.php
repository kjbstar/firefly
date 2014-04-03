<?php

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/ReportHelper.php');

use Carbon\Carbon as Carbon;

/**
 * Class ReportController
 */
class ReportController extends BaseController
{
    /**
     * Index for report controller
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $first = Toolkit::getEarliestEvent();
        $today = new Carbon;
        $first->startOfYear();
        $years = [];
        while ($first <= $today) {
            $year = $first->format('Y');
            $years[$year] = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December'
            ];
            $first->addYear();
        }


        return View::make('reports.index')->with('title', 'Reports')->with(
            'years', $years
        );
    }

    /**
     * Report for a month.
     *
     * @param $year
     * @param $month
     *
     * @return \Illuminate\View\View
     */
    public function month($year, $month)
    {
    }

    /**
     * Year report.
     *
     * @param $year
     *
     * @return \Illuminate\View\View
     */
    public function year($year)
    {
    }


    /**
     * @param $year
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function yearIeChart($year)
    {
    }

    /**
     * @param $yearOne
     * @param $yearTwo
     *
     * @return \Illuminate\View\View
     */
    public function yearCompare($yearOne, $yearTwo)
    {
    }

    /**
     * @param $yearOne
     * @param $monthOne
     * @param $yearTwo
     * @param $monthTwo
     *
     * @return \Illuminate\View\View
     */
    public function monthCompare($yearOne, $monthOne, $yearTwo, $monthTwo)
    {
    }
}