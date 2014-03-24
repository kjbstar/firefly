<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransfersComponents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create(
            'component_transfer', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transfer_id')->unsigned();
                $table->integer('component_id')->unsigned();

                $table->foreign('component_id')
                    ->references('id')->on('components')
                    ->onDelete('cascade');

                $table->foreign('transfer_id')
                    ->references('id')->on('transfers')
                    ->onDelete('cascade');

                $table->unique(['id', 'transfer_id','component_id']);

            }
        );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
