<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducerProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
     Schema::create('producer_profile', function (Blueprint $table) {
         $table->increments('id');
         $table->Integer('user_id');
         $table->Integer('track_id');
         $table->String('name');
         $table->String('image');
         $table->String('ratings');
         $table->String('description');
       });
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
