<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStoryGenresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('story_genres', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('story_id')->index('story_id');
			$table->integer('genre_id')->nullable();
			$table->integer('user_id')->nullable()->index('user_id');
			$table->integer('created_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('story_genres');
	}

}
