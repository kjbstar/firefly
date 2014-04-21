<?php

use Illuminate\Database\Migrations\Migration;

class AddAccountsPredictables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'predictables', function ($table) {
                $table->integer('account_id')->unsigned();


                $table->foreign('account_id')
                    ->references('id')->on('accounts')
                    ->onDelete('cascade');
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
