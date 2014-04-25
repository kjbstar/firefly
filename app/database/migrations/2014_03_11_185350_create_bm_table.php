<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBmTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('balancemodifiers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('account_id')->unsigned();
            $table->date('date');
            $table->decimal('balance', 10, 2);

            $table->foreign('account_id')
                ->references('id')->on('accounts')
                ->onDelete('cascade');

            $table->unique(['account_id','date']);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('balancemodifiers');
	}

}
