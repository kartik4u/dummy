<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('is_read')->default(0)->comment('1=>yes,0=>no');
			$table->integer('sender_id')->nullable()->index('sender_id');
			$table->integer('receiver_id')->nullable()->index('receiver_id');
			$table->integer('type')->comment('    1=>added new story(fav. user),2=>new apisode of reading story ,3=>fav. story new chapter.,4=>comment');
			$table->integer('story_id')->nullable();
			$table->integer('episode_id')->nullable();
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
		Schema::drop('notifications');
	}

}
