<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAdditionalUserInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('additional_user_infos', function(Blueprint $table)
		{
			$table->foreign('user_id', 'additionalfore')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('additional_user_infos', function(Blueprint $table)
		{
			$table->dropForeign('additionalfore');
		});
	}

}
