<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStoryGenresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('story_genres', function(Blueprint $table)
		{
			$table->foreign('user_id', 'storyGFor')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('story_id', 'storyGstoryForFor')->references('id')->on('stories')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('story_genres', function(Blueprint $table)
		{
			$table->dropForeign('storyGFor');
			$table->dropForeign('storyGstoryForFor');
		});
	}

}
