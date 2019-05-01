<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSavedStoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('saved_stories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('story_id')->index('story_id');
			$table->integer('user_id')->index('user_id');
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
		Schema::drop('saved_stories');
	}

}
