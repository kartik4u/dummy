<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommingSoonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comming_soons', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->text('url', 65535);
			$table->integer('user_id');
			$table->bigInteger('published_date');
			$table->bigInteger('created_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comming_soons');
	}

}
