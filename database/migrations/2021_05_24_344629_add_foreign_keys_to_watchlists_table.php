<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWatchlistItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('watchlist_items', function(Blueprint $table)
		{
			$table->foreign('exchange_id','watchlist_items_exchange_id_foreign')->references('id')->on('exchanges')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id','watchlist_items_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('watchlist_id','watchlist_items_watchlist_id_foreign')->references('id')->on('watchlists')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('watchlist_items', function(Blueprint $table)
		{
			$table->dropForeign('watchlist_items_exchange_id_foreign');
			$table->dropForeign('watchlist_items_user_id_foreign');
			$table->dropForeign('watchlist_items_watchlist_id_foreign');
		});
	}

}
