<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSavedStoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('saved_stories', function(Blueprint $table)
		{
			$table->foreign('user_id', 'savedFor')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('story_id', 'savedstoryFor')->references('id')->on('stories')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('saved_stories', function(Blueprint $table)
		{
			$table->dropForeign('savedFor');
			$table->dropForeign('savedstoryFor');
		});
	}

}
