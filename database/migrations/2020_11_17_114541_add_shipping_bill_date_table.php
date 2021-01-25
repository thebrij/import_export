<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingBillDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exporter_bills', function (Blueprint $table) {
            $table->index('shipping_bill_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exporter_bills', function (Blueprint $table) {
            $table->dropIndex(['shipping_bill_date']); // Drops index 'geo_state_index'
        });
    }
}
