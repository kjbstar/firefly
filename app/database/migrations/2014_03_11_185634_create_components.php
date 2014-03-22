<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('components', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('parent_component_id')->nullable()->unsigned();
            $table->enum('type', ['budget', 'beneficiary','category']);
            $table->string('name', 500);
            $table->boolean('reporting')->default(false);

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('parent_component_id')
                ->references('id')->on('components')
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
		Schema::drop('components');
	}

}
