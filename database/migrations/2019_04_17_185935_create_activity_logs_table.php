<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->string('meta_key')->nullable();
			$table->string('meta_value')->nullable();
			$table->integer('type')->nullable()->default(1);
			$table->boolean('status')->default(1);
			$table->bigInteger('created_at')->nullable();
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
		Schema::drop('activity_logs');
	}

}
