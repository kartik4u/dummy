<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFollowersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('followers', function(Blueprint $table)
		{
			$table->foreign('followed_by', 'user_fk_follow1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('followed_to', 'user_fk_follow2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('followers', function(Blueprint $table)
		{
			$table->dropForeign('user_fk_follow1');
			$table->dropForeign('user_fk_follow2');
		});
	}

}
