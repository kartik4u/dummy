<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFavouritesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('favourites', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('favourite_by')->index('followed_by');
			$table->integer('user_id')->index('followed_to');
			$table->integer('story_id')->nullable();
			$table->bigInteger('created_at');
			$table->boolean('type')->default(1)->comment('1=>user,2=>story');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('favourites');
	}

}
