<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducersTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('producers_tracks', function (Blueprint $table) {
          $table->increments('id');
          $table->Integer('user_id');
          $table->Integer('producer_id');
          $table->String('ratings');
          $table->String('title');
          $table->String('price');
          $table->String('views');
          $table->String('ownership_type');
          $table->String('durations');
          $table->Integer('cat_id');
          $table->Integer('visual_id');
          $table->String('tags');
          $table->Integer('is_hide')->default('0');
          $table->Integer('is_delete')->default('0');
          $table->timestamps();
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
