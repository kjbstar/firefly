<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePivots extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'component_predictable', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('predictable_id')->unsigned();
                $table->integer('component_id')->unsigned();

                $table->foreign('component_id')
                    ->references('id')->on('components')
                    ->onDelete('cascade');

                $table->foreign('predictable_id')
                    ->references('id')->on('predictables')
                    ->onDelete('cascade');

                $table->unique(['id', 'predictable_id','component_id']);

            }
        );

        Schema::create(
            'component_transaction', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('transaction_id')->unsigned();
                $table->integer('component_id')->unsigned();

                $table->foreign('transaction_id')
                    ->references('id')->on('transactions')
                    ->onDelete('cascade');

                $table->foreign('component_id')
                    ->references('id')->on('components')
                    ->onDelete('cascade');




                $table->unique(['id', 'transaction_id','component_id']);



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
        Schema::drop('component_predictable');
        Schema::drop('component_transaction');
    }

}
