<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAutherViewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('auther_views', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id');
			$table->text('message', 65535);
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
		Schema::drop('auther_views');
	}

}
