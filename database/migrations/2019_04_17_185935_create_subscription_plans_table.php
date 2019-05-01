<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_plans', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->integer('price');
			$table->boolean('status')->default(1);
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
		Schema::drop('subscription_plans');
	}

}
