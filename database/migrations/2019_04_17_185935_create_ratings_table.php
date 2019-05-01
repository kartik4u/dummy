<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRatingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ratings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('by_user_id')->nullable()->index('by_user_id');
			$table->boolean('type')->nullable();
			$table->integer('to_user_id')->nullable()->index('to_user_id');
			$table->integer('episode_id')->nullable();
			$table->integer('story_id')->nullable()->index('story_id');
			$table->float('rating', 10, 0)->nullable();
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
		Schema::drop('ratings');
	}

}
