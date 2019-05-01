<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('meta_key')->nullable();
			$table->text('meta_value', 65535);
			$table->string('name')->nullable();
			$table->integer('version')->nullable()->default(1);
			$table->boolean('status')->nullable()->default(1);
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
		Schema::drop('pages');
	}

}
