<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poops', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('duration')->nullable();

            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable()->default(null);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('poops');
    }
}
