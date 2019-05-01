<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDownloadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('downloads', function(Blueprint $table)
		{
			$table->foreign('user_id', 'downloadfore')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('story_id', 'storydownloadfore')->references('id')->on('stories')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('downloads', function(Blueprint $table)
		{
			$table->dropForeign('downloadfore');
			$table->dropForeign('storydownloadfore');
		});
	}

}
