<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sender_id')->nullable()->index('sender_id');
			$table->integer('receiver_id')->nullable()->index('receiver_id');
			$table->text('message', 16777215)->nullable();
			$table->bigInteger('created_at')->nullable();
			$table->enum('type', array('ONE2ONE','GROUP'))->default('ONE2ONE');
			$table->integer('status')->default(0);
			$table->integer('last')->default(1);
			$table->string('deleted')->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
