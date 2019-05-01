<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->float('rating', 10, 0);
			$table->integer('chapters_count');
			$table->text('description', 65535);
			$table->text('query_letter', 65535);
			$table->string('synops')->nullable();
			$table->integer('duration');
			$table->text('url', 65535);
			$table->integer('user_id')->index('user_id');
			$table->integer('favourite_count')->default(0);
			$table->float('total_revenue', 10, 0)->nullable()->default(0);
			$table->integer('share_count')->default(0);
			$table->integer('status')->default(0)->comment('0=>pending,1=>approved,2=>declined');
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
		Schema::drop('stories');
	}

}
