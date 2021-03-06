<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-05-09 at 16:59:42.
 */
class TransferControllerTest extends TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'admin')->first();
        $this->be($user);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers TransferController::index
     */
    public function testIndex()
    {
        $response = $this->action('GET', 'TransferController@index');
        $view = $response->original;
        $this->assertResponseOk();
        $this->assertTrue(count($view['transfers']) <= 25);
        $this->assertEquals('All transfers', $view['title']);
    }

    /**
     * @covers TransferController::add
     */
    public function testAdd()
    {
        $response = $this->action('GET', 'TransferController@add');
        $view = $response->original;
        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals(date('Y-m-d'), $view['prefilled']['date']);
        $this->assertEquals('Add a transfer', $view['title']);
    }

    /**
     * @covers TransferController::add
     */
    public function testAddWithOldInput()
    {
        $oldData = [
            'description' => 'Old input (new transfer)',
        ];
        $this->session(['_old_input' => $oldData]);

        $response = $this->action('GET', 'TransferController@add');
        $view = $response->original;
        $this->assertResponseOk();
        $this->assertEquals($oldData['description'], $view['prefilled']['description']);
        $this->assertEquals('Add a transfer', $view['title']);
    }

    /**
     * @covers TransferController::postAdd
     */
    public function testPostAdd()
    {
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        $data = [
            'a'              => 'b',
            'date'           => date('Y-m-d'),
            'amount'         => '100',
            'accountto_id'   => $accountTo->id,
            'accountfrom_id' => $accountFrom->id,
            'description'    => 'Add new transfer #' . rand(1000, 9999),
        ];
        $count = Transfer::count();
        $this->action('POST', 'TransferController@postAdd', $data);
        $newCount = Transfer::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count + 1, $newCount);
        $this->assertSessionHas('success');

        // remove the transfer again:
        $transfer = Transfer::where('description', $data['description'])->first();
        $transfer->delete();
    }

    /**
     * @covers TransferController::postAdd
     */
    public function testPostAddFailsValidator()
    {
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        $data = [
            'a'              => 'b',
            'date'           => date('Y-m-d'),
            'amount'         => '100',
            'accountto_id'   => $accountTo->id,
            'accountfrom_id' => $accountFrom->id,
            'description'    => null,
        ];

        $count = Transfer::count();
        $this->action('POST', 'TransferController@postAdd', $data);
        $newCount = Transfer::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
    }

    /**
     * @covers TransferController::postAdd
     */
    public function testPostAddInvalidFrom()
    {
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        $data = [
            'a'              => 'b',
            'date'           => date('Y-m-d'),
            'amount'         => '100',
            'accountto_id'   => -1,
            'accountfrom_id' => $accountFrom->id,
            'description'    => 'Bla bla.',
        ];

        $count = Transfer::count();
        $this->action('POST', 'TransferController@postAdd', $data);
        $newCount = Transfer::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
    }

    /**
     * @covers TransferController::postAdd
     */
    public function testPostAddInvalidTo()
    {
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        $data = [
            'a'              => 'b',
            'date'           => date('Y-m-d'),
            'amount'         => '100',
            'accountto_id'   => $accountTo->id,
            'accountfrom_id' => -1,
            'description'    => 'Bla bla.',
        ];

        $count = Transfer::count();
        $this->action('POST', 'TransferController@postAdd', $data);
        $newCount = Transfer::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
    }


    /**
     * @covers TransferController::edit
     */
    public function testEdit()
    {
        $transfer = Transfer::first();
        $response = $this->action('GET', 'TransferController@edit', $transfer->id);
        $view = $response->original;
        $this->assertEquals($transfer->description, $view['prefilled']['description']);
        $this->assertResponseOk();
        $this->assertSessionHas('previous');
    }

    /**
     * @covers TransferController::edit
     */
    public function testEditWithOldInput()
    {
        $oldData = [
            'description' => 'Old input (edited transfer)',
        ];
        $this->session(['_old_input' => $oldData]);

        $transfer = Transfer::first();
        $response = $this->action('GET', 'TransferController@edit', $transfer->id);
        $view = $response->original;
        $this->assertEquals($oldData['description'], $view['prefilled']['description']);
        $this->assertEquals('Edit transfer ' . $transfer->description, $view['title']);
        $this->assertResponseOk();
    }

    /**
     * @covers TransferController::postEdit
     */
    public function testPostEdit()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #1',
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $newData = [
            'description'    => 'Edited transfer for edit #' . rand(1000, 9999),
            'accountfrom_id' => $accountFrom->id,
            'accountto_id'   => $accountTo->id,
            'amount'         => 1000,
            'date'           => '2014-01-02',
        ];


        $this->call('POST', '/home/transfer/' . $transfer->id . '/edit/', $newData);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $transfer = Transfer::find($transfer->id);
        $this->assertEquals($newData['description'], $transfer->description);

        $transfer->delete();
    }

    /**
     * @covers TransferController::postEdit
     */
    public function testPostEditFailsValidation()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #' . rand(1000, 9999),
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $newData = [
            'description'    => null,
            'accountfrom_id' => $accountFrom->id,
            'accountto_id'   => $accountTo->id,
            'amount'         => 1000,
            'date'           => '2014-01-02',
        ];


        $this->call('POST', '/home/transfer/' . $transfer->id . '/edit/', $newData);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $transfer = Transfer::find($transfer->id);
        $transfer->delete();

    }

    /**
     * @covers TransferController::postEdit
     */
    public function testPostEditFailsTrigger()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #' . rand(1000, 9999),
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $newData = [
            'description'    => null,
            'accountfrom_id' => $accountFrom->id,
            'accountto_id'   => $accountTo->id,
            'amount'         => 1000,
            'date'           => '2012-01-02',
        ];


        $this->call('POST', '/home/transfer/' . $transfer->id . '/edit/', $newData);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $transfer = Transfer::find($transfer->id);
        $transfer->delete();

    }

    /**
     * @covers TransferController::postEdit
     */
    public function testPostEditFailsAccountTo()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #' . rand(1000, 9999),
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $newData = [
            'description'    => 'bla',
            'accountfrom_id' => $accountFrom->id,
            'accountto_id'   => null,
            'amount'         => 1000,
            'date'           => '2014-01-02',
        ];


        $this->call('POST', '/home/transfer/' . $transfer->id . '/edit/', $newData);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $transfer = Transfer::find($transfer->id);
        $transfer->delete();

    }

    /**
     * @covers TransferController::postEdit
     */
    public function testPostEditFailsAccountFrom()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #' . rand(1000, 9999),
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $newData = [
            'description'    => 'bla',
            'accountfrom_id' => null,
            'accountto_id'   => $accountTo->id,
            'amount'         => 1000,
            'date'           => '2014-01-02',
        ];


        $this->call('POST', '/home/transfer/' . $transfer->id . '/edit/', $newData);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $transfer = Transfer::find($transfer->id);
        $transfer->delete();

    }

    /**
     * @covers TransferController::delete
     */
    public function testDelete()
    {
        $transfer = Transfer::first();
        $response = $this->action('GET', 'TransferController@delete', $transfer->id);
        $view = $response->original;
        $this->assertEquals('Delete transfer '.$transfer->description, $view['title']);
        $this->assertResponseOk();
        $this->assertSessionHas('previous');
    }

    /**
     * @covers TransferController::postDelete
     */
    public function testPostDelete()
    {
        $user = User::where('username', 'admin')->first();
        $accountFrom = Account::first();
        $accountTo = Account::where('id', '!=', $accountFrom->id)->first();
        Eloquent::unguard();
        $transfer = Transfer::create(
            [
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
                'user_id'         => $user->id,
                'accountfrom_id'  => $accountFrom->id,
                'accountto_id'    => $accountTo->id,
                'description'     => 'Test transfer for editing #' . rand(1000, 9999),
                'amount'          => 500,
                'date'            => '2014-01-03',
                'ignoreallowance' => 0,
            ]
        );

        Eloquent::reguard();

        $count = Transfer::count();
        $this->action('POST', 'TransferController@postDelete', $transfer->id);
        $newCount = Transfer::count();
        $this->assertEquals($count-1,$newCount);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');


    }
}
