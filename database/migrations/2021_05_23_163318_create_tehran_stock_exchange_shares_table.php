<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTehranStockExchangeSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tehran_stock_exchange_shares', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code')->index();
            $table->string('group_code')->index();
            $table->string('symbol');
            $table->string('group_name');
            $table->string('instId');
            $table->string('insCode');
            $table->string('title');
            $table->string('sectorPe');
            $table->string('shareCount');
            $table->string('estimatedEps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tehran_stock_exchange_shares');
    }
}
