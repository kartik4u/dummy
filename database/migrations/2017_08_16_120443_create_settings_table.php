<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
     Schema::create('settings', function (Blueprint $table) {
         $table->increments('id');
         $table->Integer('push_notification')->default('0');
         $table->Integer('message_privacy')->default('0');;
         $table->String('terms_conditions');
         $table->String('about');
         $table->String('privacy_policy');
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
