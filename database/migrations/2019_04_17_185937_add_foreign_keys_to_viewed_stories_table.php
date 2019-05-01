<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToViewedStoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('viewed_stories', function(Blueprint $table)
		{
			$table->foreign('story_id', 'storyviewstoryGFor')->references('id')->on('stories')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'viewstoryGFor')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('viewed_stories', function(Blueprint $table)
		{
			$table->dropForeign('storyviewstoryGFor');
			$table->dropForeign('viewstoryGFor');
		});
	}

}
