<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEpisodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('episodes', function(Blueprint $table)
		{
			$table->foreign('story_id', 'Storyepisodefore')->references('id')->on('stories')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'episodefore')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('episodes', function(Blueprint $table)
		{
			$table->dropForeign('Storyepisodefore');
			$table->dropForeign('episodefore');
		});
	}

}
