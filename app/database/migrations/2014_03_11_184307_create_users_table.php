<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->softDeletes();
            $table->string('username', 250);
            $table->string('origin', 50);
            $table->string('email', 250)->nullable();
            $table->string('password', 60);
            $table->string('activation', 64)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->string('reset', 128)->nullable();

            $table->unique('username');
            $table->unique('email');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
