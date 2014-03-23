<?php


class TransactionModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testGetEmptyAttributes()
    {
        $transactions = Auth::user()->transactions()->get();
        $found = false;
        foreach ($transactions as $t) {
            $count = DB::table('component_transaction')->where('transaction_id', $t->id)->count();
            if ($count == 0) {
                $this->assertNull($t->beneficiary);
                $this->assertNull($t->category);
                $this->assertNull($t->budget);
                $found = true;
                break;
            }

        }
        if (!$found) {
            $this->assertTrue(false, 'No transactions found to test in testGetEmptyAttributes');
        }
    }

    public function testGetFilledAttributes()
    {
        $transactions = Auth::user()->transactions()->get();
        $found = false;
        foreach ($transactions as $t) {
            $count = DB::table('component_transaction')->where('transaction_id', $t->id)->count();
            if ($count >= 3) {
                $this->assertNotNull($t->beneficiary);
                $this->assertNotNull($t->category);
                $this->assertNotNull($t->budget);

                $this->assertEquals('beneficiary', $t->beneficiary->type);
                $this->assertEquals('budget', $t->budget->type);
                $this->assertEquals('category', $t->category->type);
                $found = true;
                break;
            }

        }
        if (!$found) {
            $this->assertTrue(false, 'No transactions found to test in testGetFilledAttributes');
        }
    }

    public function testAccount()
    {
        $transaction = Auth::user()->transactions()->first();
        $account = Auth::user()->accounts()->find($transaction->account_id);
        $this->assertEquals($account->name, $transaction->account()->first()->name);
        $this->assertEquals($account->id, $transaction->account()->first()->id);
    }

    public function testAttachComponent()
    {
        $transaction = Auth::user()->transactions()->orderBy(DB::Raw('RAND()'))->first();
        $count = DB::table('component_transaction')->where('transaction_id', $transaction->id)->count();
        // attach a component:
        $budget = Auth::user()->components()->where('type', 'budget')->first();
        $transaction->attachComponent($budget);
        $newCount = DB::table('component_transaction')->where('transaction_id', $transaction->id)->count();
        $this->assertEquals($count + 1, $newCount);
        $this->assertEquals($budget->id, $transaction->budget->id);

    }

    public function testAttachEmptyComponent()
    {
        $transaction = Auth::user()->transactions()->orderBy(DB::Raw('RAND()'))->first();
        $count = DB::table('component_transaction')->where('transaction_id', $transaction->id)->count();
        // attach a component:
        $transaction->attachComponent(null);
        $newCount = DB::table('component_transaction')->where('transaction_id', $transaction->id)->count();
        $this->assertEquals($count, $newCount);
        $this->assertNull($transaction->budget);

    }

    public function testScopeInMonth()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transactions()->where(
            DB::Raw('DATE_FORMAT(`date`,"%m-%Y")'), '=',
            $date->format('m-Y')
        )->count();
        $count = Auth::user()->transactions()->inMonth($date)->count();
        $this->assertEquals($raw, $count);
    }

    public function testScopeInYear()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transactions()->where(DB::Raw('DATE_FORMAT(`date`,"%Y")'), '=', $date->format('Y'))->count(
        );
        $count = Auth::user()->transactions()->inYear($date)->count();
        $this->assertEquals($raw, $count);
    }

    public function testScopeOnDay()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transactions()->where('date', '=', $date->format('Y-m-d'))->count();
        $count = Auth::user()->transactions()->onDay($date)->count();
        $this->assertEquals($raw, $count);
    }

    public function testScopeBetweenDates()
    {
        $start = new \Carbon\Carbon();
        $start->subYear();
        $end = clone $start;
        $end->addMonths(2);
        $raw = Auth::user()->transactions()->where('date', '>=', $start->format('Y-m-d'))->where('date', '<=', $end->format('Y-m-d'))->count();
        $count = Auth::user()->transactions()->betweenDates($start,$end)->count();
        $this->assertEquals($raw, $count);
    }

    public function testScopeExpenses()
    {
        $raw = Auth::user()->transactions()->where('amount', '<=',0)->count();
        $count = Auth::user()->transactions()->expenses()->count();
        $this->assertEquals($raw, $count);
    }

    public function testScopeAfterDate()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transactions()->where('date', '>=', $date->format('Y-m-d'))->count();
        $count = Auth::user()->transactions()->afterDate($date)->count();
        $this->assertEquals($raw, $count);

    }

    public function testScopeIncomes()
    {
        $raw = Auth::user()->transactions()->where('amount', '>',0.0)->count();
        $count = Auth::user()->transactions()->incomes()->count();
        $this->assertEquals($raw, $count);
    }

    public function testUser()
    {
        $transaction = Auth::user()->transactions()->first();
        $this->assertEquals(Auth::user()->id, $transaction->user()->first()->id);
    }

    public function testPredictable()
    {
        $transaction = Auth::user()->transactions()->whereNotNull('predictable_id')->first();
        $this->assertEquals($transaction->predictable_id,$transaction->predictable()->first()->id);
    }

    public function testGetDescriptionAttribute()
    {
        $transaction = Auth::user()->transactions()->first();
        $transaction->description = 'Bla bla';
        $this->assertEquals('Bla bla',$transaction->description);
        $transaction->description = null;
        $this->assertNull($transaction->description);
    }

    public function testGetDates()
    {
        $transaction = Auth::user()->transactions()->first();
        $this->assertInstanceOf('\Carbon\Carbon', $transaction->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $transaction->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $transaction->date);
    }

} 