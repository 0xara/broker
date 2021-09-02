<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlertsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alerts', function(Blueprint $table)
		{
			$table->foreign('exchange_id','alerts_exchange_id_foreign')->references('id')->on('exchanges')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id','alerts_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('alerts', function(Blueprint $table)
		{
			$table->dropForeign('alerts_exchange_id_foreign');
			$table->dropForeign('alerts_user_id_foreign');
		});
	}

}
