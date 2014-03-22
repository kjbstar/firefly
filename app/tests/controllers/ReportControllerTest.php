<?php

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

        $date = new \Carbon\Carbon();
        $date->subMonth();
        $date->startOfMonth();
        // in our test data, we always make more than we spend.
        $response = $this->call(
            'GET', 'home/reports/period/' . $date->format('Y/m')
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            'Report for ' . $date->format('F Y'), $view['title']
        );
        $this->assertEquals($date, $view['start']);
        $this->assertLessThanOrEqual(
            $view['sums']['sumIn'], $view['sums']['sumOut']
        );
        $this->assertLessThanOrEqual(
            $view['netWorth']['end'], $view['netWorth']['start']
        );
        // since we go for the current month, no predicted transactions:
        $this->assertCount(0, $view['transactions']['predicted']);
    }

    public function testOldMonth()
    {

        $date = new \Carbon\Carbon();
        $date->subYear();
        $date->startOfMonth();
        // in our test data, we always make more than we spend.
        $response = $this->call(
            'GET', 'home/reports/period/' . $date->format('Y/m')
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            'Report for ' . $date->format('F Y'), $view['title']
        );
        $this->assertEquals($date, $view['start']);
        $this->assertLessThanOrEqual(
            $view['sums']['sumIn'], $view['sums']['sumOut']
        );
        $this->assertLessThanOrEqual(
            $view['netWorth']['end'], $view['netWorth']['start']
        );
        // since we go for an old month, both predictables fired.
        $count = Auth::user()->predictables()->count();
        $this->assertCount($count, $view['transactions']['predicted']);
    }

//
    public function testYear()
    {
        $date = new \Carbon\Carbon();
        $response = $this->call(
            'GET', 'home/reports/period/' . $date->format('Y')
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Report for ' . $date->format('Y'), $view['title']);
        $this->assertEquals($date->format('Y'), $view['year']);
        $this->assertLessThanOrEqual(
            $view['endNetWorth'], $view['startNetWorth']
        );
        $this->assertLessThanOrEqual(
            $view['totalIncome'], $view['totalExpenses']
        );

    }

//
    public function testYearIeChart()
    {
        $this->call('GET', 'home/reports/year/' . date('Y') . '/ie');
        $this->assertResponseStatus(200);

        $jsonResponse = $this->client->getResponse()->getContent();
        $responseData = json_decode($jsonResponse, true);
        $this->assertArrayHasKey('cols', $responseData);
        $this->assertArrayHasKey('rows', $responseData);
        // count:
        $this->assertCount(4, $responseData['cols']);
        $this->assertCount(12, $responseData['rows']);
    }

//
    public function testYearCompare()
    {
        $two = new \Carbon\Carbon();
        $two->startOfYear();
        $two->subYear();
        $one = clone $two;
        $one->subYear();

        $response = $this->call(
            'GET',
            'home/reports/compare/' . $one->format('Y') . '/' . $two->format(
                'Y'
            )
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            'Comparing ' . $one->format('Y') . ' with ' . $two->format('Y'),
            $view['title']
        );
        // no further tests possible.
        $this->assertEquals($one, $view['one']);
        $this->assertEquals($two, $view['two']);
    }

//
    public function testMonthCompare()
    {
        $two = new \Carbon\Carbon();
        $two->startOfMonth();
        $two->subMonths(2);
        $one = clone $two;
        $one->subYear();

        $response = $this->call(
            'GET',
            'home/reports/compare/' . $one->format('Y-m') . '/' . $two->format(
                'Y-m'
            )
        );
        $this->assertResponseStatus(200);
        $view = $response->original;
        $this->assertEquals(
            'Comparing ' . $one->format('F Y') . ' with ' . $two->format('F Y'),
            $view['title']
        );
        $this->assertEquals($one, $view['one']);
        $this->assertEquals($two, $view['two']);
        // validate some numbers:
        // in test, we always make shit loads of money:
        $this->assertLessThanOrEqual(
            $view['numbers']['two']['net_start'],
            $view['numbers']['one']['net_start']
        );
        $this->assertLessThanOrEqual(
            $view['numbers']['two']['net_end'],
            $view['numbers']['one']['net_end']
        );

    }

//
    public function testMonthCompareAccountChart()
    {
        $two = new \Carbon\Carbon();
        $two->startOfMonth();
        $two->subMonths(2);
        $one = clone $two;
        $one->subYear();

        $this->call(
            'GET',
            'home/reports/compare/' . $one->format('Y-m') . '/' . $two->format(
                'Y-m'
            ) . '/account'
        );
        $this->assertResponseStatus(200);

        $jsonResponse = $this->client->getResponse()->getContent();
        $responseData = json_decode($jsonResponse, true);
        $this->assertArrayHasKey('cols', $responseData);
        $this->assertArrayHasKey('rows', $responseData);
        $this->assertCount(3, $responseData['cols']);
        $this->assertCount(intval($one->format('t')), $responseData['rows']);

    }

    public function testYearComponentsChart()
    {
        $comp = Auth::user()->components()->reporting()->count();
        $this->call('GET', 'home/reports/year/' . date('Y') . '/components');
        $this->assertResponseStatus(200);

        $jsonResponse = $this->client->getResponse()->getContent();
        $responseData = json_decode($jsonResponse, true);
        $this->assertArrayHasKey('cols', $responseData);
        $this->assertArrayHasKey('rows', $responseData);
        $this->assertCount($comp + 1, $responseData['cols']);
        $this->assertCount(12, $responseData['rows']);
    }

    public function testSameMonthCompare()
    {
        $one = new \Carbon\Carbon();
        $one->startOfMonth();
        $crawler = $this->client->request(
            'GET',
            'home/reports/compare/' . $one->format('Y-m') . '/' . $one->format(
                'Y-m'
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("HTTP Error: 500")'));
    }

    public function testSameYearCompare()
    {
        $one = new \Carbon\Carbon();
        $one->startOfMonth();
        $crawler = $this->client->request(
            'GET',
            'home/reports/compare/' . $one->format('Y') . '/' . $one->format(
                'Y'
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("HTTP Error: 500")'));
    }

    public function testSameMonthCompareAccountChart()
    {
        $one = new \Carbon\Carbon();

        $crawler = $this->client->request(
            'GET',
            'home/reports/compare/' . $one->format('Y-m') . '/' . $one->format(
                'Y-m'
            ) . '/account'
        );
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("HTTP Error: 500")'));

    }

// test monthcompareaccountchart for same month.
}