<?php


class SettingModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testFindSetting()
    {
        $notExisting = Setting::findSetting('DOES NOT COMPUTE');
        $this->assertNull($notExisting);
    }

    public function testFindExistingSetting()
    {
        $setting = Setting::create([
                'user_id' => Auth::user()->id,
                'type' => 'string',
                'name' => 'devSetting',
                'date' => null,
                'value' => 'Hi!'

            ]);
        $existing = Setting::findSetting('devSetting');
        $this->assertNotNull($existing);
        $this->assertEquals($setting->user_id,$existing->user_id);
        $this->assertEquals($setting->value,$existing->value);
    }

    public function testFindStringSetting()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'string',
            'name' => 'devSettingString',
            'date' => null,
            'value' => '  Hi!  '

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingString');
        $this->assertNotNull($existing);
        $this->assertEquals($data['user_id'],$existing->user_id);
        $this->assertEquals(trim($data['value']),$existing->value);
    }
    public function testFindFloatSetting()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'float',
            'name' => 'devSettingFloat',
            'date' => null,
            'value' => '1.2'

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingFloat');
        $this->assertNotNull($existing);
        $this->assertEquals($data['user_id'],$existing->user_id);
        $this->assertEquals(floatval($data['value']),$existing->value);
    }
    public function testFindIntSetting()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'int',
            'name' => 'devSettingInt',
            'date' => null,
            'value' => '122'

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingInt');
        $this->assertNotNull($existing);
        $this->assertEquals($data['user_id'],$existing->user_id);
        $this->assertEquals(intval($data['value']),$existing->value);
    }
    public function testFindDateSetting()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'date',
            'name' => 'devSettingDate',
            'date' => null,
            'value' => '2012-01-01'

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingDate');
        $this->assertNotNull($existing);
        $this->assertEquals($data['user_id'],$existing->user_id);
        $this->assertEquals(new \Carbon\Carbon($data['value']),$existing->value);
    }

    public function testUser()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'date',
            'name' => 'devSettingUser',
            'date' => null,
            'value' => '2012-01-01'

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingUser');
        $this->assertEquals($data['user_id'],$existing->user()->first()->id);

    }

    public function testGetSetting() {
        $setting = Setting::getSetting('predictionStart');

        $this->assertEquals(Config::get('firefly.predictionStart.type'),$setting->type);
        $this->assertEquals(Config::get('firefly.predictionStart.value'),$setting->value->format('Y-m-d'));

    }

    public function testGetDates()
    {
        $data = [
            'user_id' => Auth::user()->id,
            'type' => 'string',
            'name' => 'devSettingWithDate',
            'date' => '2012-01-01',
            'value' => 'blablabla'

        ];

        Setting::create($data);
        $existing = Setting::findSetting('devSettingWithDate');
        $this->assertInstanceOf('\Carbon\Carbon', $existing->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $existing->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $existing->date);
    }

    public static function tearDownAfterClass()
    {
        DB::table('settings')->delete();
    }

} 