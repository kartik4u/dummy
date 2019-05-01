<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateViewedStoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('viewed_stories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->boolean('type')->default(1)->comment('1=>story,2=>episode');
			$table->integer('story_id')->index('story_id_2');
			$table->integer('episode_id');
			$table->boolean('is_full_watched')->default(0);
			$table->float('perview_amount', 10, 0)->default(0.02);
			$table->float('full_amount', 10, 0)->default(0.03);
			$table->bigInteger('created_at');
			$table->bigInteger('updated_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('viewed_stories');
	}

}
