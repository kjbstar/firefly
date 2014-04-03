<?php

/**
 * Class TransactionControllerTest
 */
class TransactionControllerTest extends TestCase
{

    private $_amount = 123.40;

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testShowIndex()
    {
        $response = $this->call('GET', 'home/transaction');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('All transactions', $view['title']);
        $this->assertCount(25, $view['transactions']);
    }

    public function testAdd()
    {
        $accounts = Auth::user()->accounts()->count();
        $response = $this->call('GET', 'home/transaction/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals($view['title'], 'Add a transaction');
        $this->assertCount($accounts, $view['accounts']);
    }

    public function testAddWithPredictable()
    {
        $accounts = Auth::user()->accounts()->count();
        $predictable = Auth::user()->predictables()->first();
        $response = $this->call(
            'GET', 'home/transaction/add/' . $predictable->id
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals($view['title'], 'Add a transaction');
        $this->assertEquals($view['prefilled']['description'], $predictable->description);
        $this->assertCount($accounts, $view['accounts']);
    }

    public function testAddWithOldInput()
    {
        $this->session(['_old_input' => ['description' => 'Test', 'amount' => 100]]);
        $accounts = Auth::user()->accounts()->count();
        $response = $this->call(
            'GET', 'home/transaction/add/'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($view['prefilled']['description'], 'Test');
        $this->assertEquals($view['title'], 'Add a transaction');
        $this->assertCount($accounts, $view['accounts']);
    }

    public function testEmptyPostAdd()
    {
        $count = Auth::user()->transactions()->count();
        $this->call('POST', 'home/transaction/add');
        $newCount = Auth::user()->transactions()->count();
        $this->assertEquals($count, $newCount);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addtransaction');
        $this->assertHasOldInput();

    }


    public function testPostAdd()
    {
        $count = Auth::user()->transactions()->count();
        $account = Auth::user()->accounts()->first();

        $data = ['account_id'  => $account->id,
                 'description' => 'Test',
                 'category'    => 'TestCategory #1', // existing
                 'beneficiary' => 'TestBeneficiary #1', // existing
                 'budget'      => 'Something', // new
                 'amount'      => $this->_amount, 'date' => date('Y-m-d')
        ];

        $this->call('POST', 'home/transaction/add', $data);
        $newCount = Auth::user()->transactions()->count();
        $this->assertResponseStatus(302);

        $this->assertRedirectedToRoute('index');
        $this->assertEquals($count + 1, $newCount);
        $this->assertSessionHas('success');
    }

    /**
     * The amount  = * 2 because we need it later.
     */
    public function testPostAddParentComponents()
    {
        $count = Auth::user()->transactions()->count();
        $account = Auth::user()->accounts()->first();
        $data = ['account_id'  => $account->id, 'description' => 'Test',
                 'category'    => 'TestCategory #1', // existing
                 'beneficiary' => 'TestBeneficiary #1', // existing
                 'budget'      => 'SomethingElse/Else', // new
                 'amount'      => $this->_amount * 2, 'date' => date('Y-m-d')

        ];
        $this->call('POST', 'home/transaction/add', $data);
        $newCount = Auth::user()->transactions()->count();
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertEquals($count + 1, $newCount);
        $this->assertSessionHas('success');
    }

    public function testPostFailValidator()
    {
        $count = Auth::user()->transactions()->count();
        $account = Auth::user()->accounts()->first();
        $data = ['account_id'  => $account->id, 'description' => 'Test',
                 'category'    => 'TestCategory #1', // existing
                 'beneficiary' => 'TestBeneficiary #1', // existing
                 'budget'      => 'Wow', // new
                 'amount'      => $this->_amount, null

        ];
        $this->call('POST', 'home/transaction/add', $data);
        $newCount = Auth::user()->transactions()->count();
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addtransaction');
        $this->assertEquals($count, $newCount);
        $this->assertHasOldInput();
    }


    /**
     * @depends testPostAdd
     */
    public function testEdit()
    {
        $accounts = Auth::user()->accounts()->count();
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount)->first();
        $response = $this->call(
            'GET', 'home/transaction/' . $transaction->id . '/edit'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            $view['title'], 'Edit transaction ' . $transaction->description
        );
        $this->assertCount($accounts, $view['accounts']);
    }

    /**
     * @depends testPostAdd
     */
    public function testPostEdit()
    {
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount)->first();
        $account = Auth::user()->accounts()->first();
        $data = ['description' => 'TestEdit', 'amount' => $this->_amount,
                 'date'        => date('Y-m-d'), 'account_id' => $account->id];

        $this->call(
            'POST', 'home/transaction/' . $transaction->id . '/edit', $data
        );
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    /**
     * Leave everything as is except the account.
     *
     * @depends testPostAdd
     */
    public function testPostEditChangeAccounts()
    {
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount * 2)->first();
        // get another account:
        $newAccount = Auth::user()->accounts()->where('id', '!=', $transaction->account_id)->first();
        $data = ['description' => 'TestEdit', 'amount' => $this->_amount,
                 'date'        => date('Y-m-d'), 'account_id' => $newAccount->id,
                 'budget'      => $transaction->budget->name,
                 'beneficiary' => $transaction->beneficiary->name,
        ];

        $this->call(
            'POST', 'home/transaction/' . $transaction->id . '/edit', $data
        );
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }


    public function testPostEditFail()
    {
        $transaction = Auth::user()->transactions()->first();
        $data = ['amount' => 20, 'date' => date('Y-m-d'),];

        $this->call(
            'POST', 'home/transaction/' . $transaction->id . '/edit', $data
        );
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('edittransaction', $transaction->id);
    }

    /**
     * @depends testPostAdd
     */
    public function testPostEditComponents()
    {
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount)->first();
        $account = Auth::user()->accounts()->first();
        $data = ['description' => 'TestEdit', 'amount' => $this->_amount,
                 'date'        => date('Y-m-d'), 'account_id' => $account->id,
                 'category'    => 'TestCategory #1', // existing
                 'beneficiary' => 'TestBeneficiary #1', // existing
                 'budget'      => 'Wow2', // new

        ];

        $this->call(
            'POST', 'home/transaction/' . $transaction->id . '/edit', $data
        );
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');

        $this->assertRedirectedToRoute('index');
    }

    /**
     * @depends testPostAdd
     */
    public function testPostEditParentComponents()
    {
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount)->first();
        $account = Auth::user()->accounts()->first();
        $data = ['description' => 'TestEdit', 'amount' => $this->_amount,
                 'date'        => date('Y-m-d'), 'account_id' => $account->id,
                 'category'    => 'TestCategory #1', // existing
                 'beneficiary' => 'TestBeneficiary #1', // existing
                 'budget'      => 'IAm/VerySpecial', // new

        ];
        $this->call(
            'POST', 'home/transaction/' . $transaction->id . '/edit', $data
        );
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testDelete()
    {
        $transaction = Auth::user()->transactions()->first();
        $response = $this->call(
            'GET', 'home/transaction/' . $transaction->id . '/delete'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            $view['title'], 'Delete transaction ' . $transaction->description
        );
    }

    /**
     * @depends testPostAdd
     */
    public function testPostDelete()
    {
        $count = Auth::user()->transactions()->count();
        $transaction = Auth::user()->transactions()->where('amount', $this->_amount)->first();
        $this->call('POST', 'home/transaction/' . $transaction->id . '/delete');
        $newCount = Auth::user()->transactions()->count();
        $this->assertEquals($count - 1, $newCount);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');

        $this->assertRedirectedToRoute('index');
    }

    public static function tearDownAfterClass()
    {
        DB::table('transactions')->where('amount', 123.40)->delete();
        DB::table('transactions')->where('amount', 123.40 * 2)->delete();

        DB::table('components')->where('reporting', 0)->delete();
    }

} 