<?php

class TransferControllerTest extends TestCase
{
    private $amount = 123.45;

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testShowIndex()
    {
        $response = $this->call('GET', 'home/transfer');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('All transfers', $view['title']);
        $this->assertLessThanOrEqual(50, count($view['transfers']));

    }

    public function testAdd()
    {
        $accounts = Auth::user()->accounts()->count();
        $response = $this->call('GET', 'home/transfer/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals($view['title'], 'Add a transfer');
        $this->assertCount($accounts, $view['accounts']);
    }

    public function testPostAdd()
    {
        $count = Auth::user()->transfers()->count();
        $accountto = Auth::user()->accounts()->first();
        $accountfrom = Auth::user()->accounts()->where(
            'id', '!=', $accountto->id
        )->first();
        $data = ['accountfrom_id' => $accountfrom->id,
                 'accountto_id'   => $accountto->id,
                 'description'    => 'TestTransfer', 'amount' => $this->amount,
                 'date'           => date('Y-m-d')

        ];
        $this->call('POST', 'home/transfer/add', $data);
        $newCount = Auth::user()->transfers()->count();
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
        $this->assertEquals($count + 1, $newCount);

    }

    public function testPostAddFail()
    {
        $count = Auth::user()->transfers()->count();
        $data = ['amount' => 1, 'date' => date('Y-m-d')

        ];
        $this->call('POST', 'home/transfer/add', $data);
        $newCount = Auth::user()->transfers()->count();
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addtransfer');
        $this->assertEquals($count, $newCount);
        $this->assertHasOldInput();

    }

    public function testEdit()
    {
        $accounts = Auth::user()->accounts()->count();
        $transfer = Auth::user()->transfers()->where('amount',$this->amount)->first();
        $response = $this->call(
            'GET', 'home/transfer/' . $transfer->id . '/edit'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            $view['title'], 'Edit transfer ' . $transfer->description
        );
        $this->assertCount($accounts, $view['accounts']);
    }

    public function testPostEdit()
    {
        $transfer = Auth::user()->transfers()->where('amount',$this->amount)->first();
        $data = ['description' => 'TestEdit', 'amount' => $this->amount,
                 'date' => date('Y-m-d'),
                 'accountfrom_id' => $transfer->accountto_id,
                 'accountto_id' => $transfer->accountfrom_id];

        $this->call('POST', 'home/transfer/' . $transfer->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testPostFailedEdit()
    {
        $transfer = Auth::user()->transfers()->first();
        $data = ['description' => null, 'amount' => 20,
                 'date'        => date('Y-m-d'),];

        $this->call('POST', 'home/transfer/' . $transfer->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('edittransfer', $transfer->id);
    }

    public function testDelete()
    {
        $transfer = Auth::user()->transfers()->where('amount',$this->amount)->first();
        $response = $this->call(
            'GET', 'home/transfer/' . $transfer->id . '/delete'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            $view['title'], 'Delete transfer ' . $transfer->description
        );
    }

    public function testPostDelete()
    {
        $count = Auth::user()->transfers()->count();
        $transfer = Auth::user()->transfers()->where('amount',$this->amount)->first();
        $this->call('POST', 'home/transfer/' . $transfer->id . '/delete');
        $newCount = Auth::user()->transfers()->count();
        $this->assertEquals($count - 1, $newCount);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');

        $this->assertRedirectedToRoute('index');
    }
} 