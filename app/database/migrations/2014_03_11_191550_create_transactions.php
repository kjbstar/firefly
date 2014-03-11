<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('predictable_id')->nullable()->unsigned();
            $table->string('description', 500);
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->boolean('ignoreprediction')->default(false);
            $table->boolean('ignoreallowance')->default(false);
            $table->boolean('mark')->default(false);

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('account_id')
                ->references('id')->on('accounts')
                ->onDelete('cascade');

            $table->foreign('predictable_id')
                ->references('id')->on('predictables')
                ->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transactions');
	}

}
