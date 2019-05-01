<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEpisodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('episodes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('story_id')->index('story_id');
			$table->integer('user_id')->nullable()->index('user_id');
			$table->string('name');
			$table->text('description', 65535)->nullable();
			$table->float('revenue', 10, 0)->default(0);
			$table->string('synops')->nullable();
			$table->integer('view_count')->nullable()->default(0);
			$table->integer('share_count')->nullable()->default(0);
			$table->float('revenue_full_read', 10, 0)->default(0);
			$table->integer('rating')->default(0);
			$table->boolean('status')->default(0)->comment('1=>approve,2=>decline,0=>panding');
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
		Schema::drop('episodes');
	}

}
