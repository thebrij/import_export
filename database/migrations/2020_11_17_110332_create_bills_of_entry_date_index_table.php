<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsOfEntryDateIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('importer_bills', function ($table) {
            $table->index('bill_of_entry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('importer_bills', function ($table) {
            $table->dropIndex(['bill_of_entry_date']); // Drops index 'geo_state_index'
        });
    }
}
