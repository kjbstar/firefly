<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePredictables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('predictables', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->string('description', 500);
            $table->decimal('amount', 10, 2);
            $table->smallInteger('dom')->unsigned()->default(1);
            $table->smallInteger('pct')->unsigned()->default(10);
            $table->boolean('inactive')->default(false);

            $table->foreign('user_id')
                ->references('id')->on('users')
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
		Schema::drop('predictables');
	}

}
