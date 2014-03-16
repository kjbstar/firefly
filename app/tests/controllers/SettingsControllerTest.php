<?php

/**
 * Created by PhpStorm.
 * User: sander
 * Date: 15/03/14
 * Time: 09:56
 */
class SettingsControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);

    }


    public function testIndex()
    {
        $response = $this->call('GET', 'home/settings');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Settings', $view['title']);
        // prediction start should equal configuration:
        $predictionStart = Config::get('firefly.predictionStart');
        $this->assertEquals(
            new \Carbon\Carbon($predictionStart['value']),
            $view['predictionStart']
        );
        $account = Auth::user()->accounts()->first();
        $this->assertEquals($account, $view['frontpageAccount']);

    }

    public function testPostIndex()
    {
        $account = Auth::user()->accounts()->first();
        $data = ['predictionStart' => '2013-01-01',
                 'frontpageAccount' => $account->id];
        $response = $this->call('POST', 'home/settings', $data);

        // validate result:
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');

        // validate settings are saved:
        $predictionStart = Auth::user()->settings()->where(
            'name', 'predictionStart'
        )->first();
        $this->assertEquals(
            $predictionStart->value,
            new \Carbon\Carbon($data['predictionStart'])
        );

        $frontpageAccount = Auth::user()->settings()->where(
            'name', 'frontpageAccount'
        )->first();
        $this->assertEquals(
            $frontpageAccount->value, $data['frontpageAccount']
        );

    }

    public function testAllowances()
    {
        $response = $this->call('GET', 'home/allowances');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Allowances', $view['title']);

        // default allowance should match setting:
        $defaultAllowance = Config::get('firefly.defaultAllowance');
        $this->assertEquals(
            $defaultAllowance['value'], $view['defaultAllowance']->value
        );

        // no specific allowances should exist.
        $this->assertCount(0, $view['allowances']);

    }

    public function testPostAllowances()
    {
        // set the default allowance to 1500.
        $data = ['defaultAllowance' => 1500];
        $this->call('POST', 'home/allowances', $data);
        $this->assertResponseStatus(302);

        // setting must be updated.
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
        $defaultAllowance = Auth::user()->settings()->where(
            'name', 'defaultAllowance'
        )->first();
        $this->assertEquals(
            $data['defaultAllowance'], $defaultAllowance->value
        );

    }

    public function testAddAllowance()
    {
        $response = $this->call('GET', 'home/allowances/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals('Add a new allowance', $view['title']);

    }

    public function testPostAddAllowance()
    {
        $date = new \Carbon\Carbon();
        $data = ['date' => $date->format('Y-m'), 'amount' => 1400];
        $this->call('POST', 'home/allowances/add', $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');

        $setting = Auth::user()->settings()->where('name', 'specificAllowance')
            ->where('date', $date->format('Y-m') . '-01')->with(
            'value', $data['amount']
        )->count();
        $this->assertEquals(1,$setting);


    }

    public function testPostAddInvalidAllowance()
    {
        $date = new \Carbon\Carbon();
        $count = Auth::user()->settings()->where('name', 'specificAllowance')->count();
        $data = ['date' => $date->format('Y-m'), 'amount' => null];
        $this->call('POST', 'home/allowances/add', $data);
        $newCount = Auth::user()->settings()->where('name', 'specificAllowance')->count();
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('error');
        $this->assertEquals($count,$newCount);
    }

    public function testEditAllowance()
    {
        $setting = Auth::user()->settings()->where('name','specificAllowance')->first();
        $response = $this->call('GET', 'home/allowance/'.$setting->id.'/edit');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals($setting->amount,$view['setting']->amount);

    }

    public function testPostEditAllowance()
    {
        $data = ['value' => 1300];
        $setting = Auth::user()->settings()->where('name','specificAllowance')->first();
        $this->call('POST', 'home/allowance/'.$setting->id.'/edit',$data);
        $count = Auth::user()->settings()->where('name','specificAllowance')->where('value',$data['value'])->count();

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
        $this->assertEquals(1,$count);

    }

    public function testPostEditInvalidAllowance()
    {
        $data = ['value' => 0];
        $setting = Auth::user()->settings()->where('name','specificAllowance')->first();
        $this->call('POST', 'home/allowance/'.$setting->id.'/edit',$data);
        $count = Auth::user()->settings()->where('name','specificAllowance')->where('value',$data['value'])->count();

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('error');
        $this->assertEquals(0,$count);
    }

    public function testDeleteAllowance()
    {
        $setting = Auth::user()->settings()->where('name','specificAllowance')->first();
        $response = $this->call('GET', 'home/allowance/'.$setting->id.'/delete');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals($setting->amount,$view['setting']->amount);
    }

    public function testDostDeleteInvalidAllowance()
    {
        $setting = Auth::user()->settings()->where('name','specificAllowance')->first();
        $id = $setting->id;
        $this->call('POST', 'home/allowance/'.$setting->id.'/delete');
        $deleted = Auth::user()->settings()->find($id);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
        $this->assertNull($deleted);

    }

    public static function tearDownAfterClass()
    {
        DB::table('settings')->delete();
    }



} 