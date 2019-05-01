<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('role_id')->index('fk_users_roles_idx')->comment('1=>admin,2=>reader,3=>writer');
			$table->string('device_id')->nullable();
			$table->string('fb_id')->nullable();
			$table->string('gmail_id')->nullable();
			$table->string('device_type')->nullable()->default('ANDROID')->comment('IOS,ANDROID');
			$table->string('name')->nullable();
			$table->string('username')->nullable();
			$table->bigInteger('dob')->nullable();
			$table->string('gender')->nullable();
			$table->string('signup_type')->default('normal');
			$table->string('slug')->nullable();
			$table->string('email')->nullable();
			$table->string('password')->nullable();
			$table->string('country')->nullable();
			$table->string('city')->nullable();
			$table->string('address')->nullable();
			$table->string('postal_code')->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('profile_image')->nullable();
			$table->bigInteger('phone')->nullable();
			$table->integer('subscription_plan_id')->nullable()->index('subscription_plan_id');
			$table->boolean('is_subscription')->default(0);
			$table->bigInteger('subscription_start_date')->nullable();
			$table->string('forgot_password_token')->nullable();
			$table->string('verification_token')->nullable();
			$table->text('verify_token', 65535)->nullable();
			$table->bigInteger('profile_submited_at')->nullable();
			$table->boolean('privacy_version')->default(1);
			$table->boolean('termsandcondition_version')->default(1);
			$table->boolean('push_notification_status')->default(1)->comment('1=>on,0=>off');
			$table->boolean('is_promotional_email_allow')->default(0);
			$table->bigInteger('last_login')->nullable();
			$table->string('stripe_id')->nullable();
			$table->bigInteger('current_login')->nullable();
			$table->integer('stories_count')->default(0);
			$table->integer('followers_count')->nullable()->default(0);
			$table->integer('favourite_count')->default(0);
			$table->float('avg_rating', 10, 0)->nullable()->default(0);
			$table->string('synopsis')->nullable();
			$table->string('about_writer')->nullable();
			$table->float('total_revenue', 10, 0)->default(0);
			$table->float('monthly_revenue', 10, 0)->default(0);
			$table->text('auth_token', 65535)->nullable();
			$table->boolean('status')->nullable()->default(0)->comment('0=>not verified, 1 => Active or approved, 2=> Deactivated by Admin');
			$table->boolean('auther_status')->default(0)->comment('0=>pending,1=>approved,3=>declined');
			$table->boolean('is_writer_reader')->comment('0=>no,1=>yes');
			$table->text('remember_token', 65535);
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
		Schema::drop('users');
	}

}
