<?php

use Carbon\Carbon as Carbon;

/**
 * Class ReportControllerTest
 */
class ReportControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);

    }

    public function testIndex()
    {
        $response = $this->call('GET', 'home/reports');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Reports', $view['title']);
        foreach ($view['years'] as $year) {
            $this->assertCount(12, $year);
        }
    }


    public function testMonth()
    {
        $date = new Carbon;
        $response = $this->call('GET', 'home/reports/period/' . $date->format('Y/m'));
        $this->assertResponseStatus(200);
        $view = $response->original;
        $this->assertEquals('Report for ' . $date->format('F Y'), $view['title']);
    }

    public function testMonthChart()
    {
        $date = new Carbon;
        $response = $this->call('GET', 'home/reports/period/' . $date->format('Y/m').'/chart');
        $this->assertResponseStatus(200);
    }

    public function testYear()
    {
        $date = new Carbon;
        $response = $this->call('GET', 'home/reports/period/' . $date->format('Y'));

        $this->assertResponseStatus(200);
        $view = $response->original;
        $this->assertEquals('Report for ' . $date->format('Y'), $view['title']);
        $this->assertCount(12,$view['months']);
    }

    public function testYearChart()
    {
        $date = new Carbon;
        $this->call('GET', 'home/reports/period/' . $date->format('Y').'/chart');
        $this->assertResponseStatus(200);
    }
}