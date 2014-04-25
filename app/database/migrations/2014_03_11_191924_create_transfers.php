<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transfers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->integer('accountfrom_id')->unsigned();
            $table->integer('accountto_id')->unsigned();
            $table->string('description', 500);
            $table->decimal('amount', 10, 2)->unsigned();
            $table->date('date');
            $table->boolean('ignoreallowance')->default(false);

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('accountfrom_id')
                ->references('id')->on('accounts')
                ->onDelete('cascade');

            $table->foreign('accountto_id')
                ->references('id')->on('accounts')
                ->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transfers');
	}

}
