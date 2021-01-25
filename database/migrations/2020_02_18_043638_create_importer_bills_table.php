<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImporterBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importer_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string       ('bill_of_entry_no');
            $table->date         ('bill_of_entry_date')->nullable();
            $table->string       ('importer_id')->nullable();
            $table->string       ('importer_name')->nullable();
            $table->string       ('importer_address')->nullable();
            $table->string       ('importer_city_state')->nullable();
            $table->string       ('pin_code')->nullable();
            $table->string       ('city')->nullable();
            $table->string       ('state')->nullable();
            $table->string       ('contact_no')->nullable();
            $table->string       ('email')->nullable();
            $table->string       ('supplier')->nullable();
            $table->string       ('supplier_address')->nullable();
            $table->string       ('foreign_port')->nullable();
            $table->string       ('origin_country')->nullable();
            $table->string       ('hs_code')->nullable();
            $table->string       ('chapter')->nullable();
            $table->string       ('product_discription')->nullable();
            $table->string       ('quantity')->nullable();
            $table->string       ('unit_quantity')->nullable();
            $table->string       ('unit_value_as_per_invoice')->nullable();
            $table->string       ('invoice_currency')->nullable();
            $table->string       ('total_value_in_fc')->nullable();
            $table->string       ('unit_rate_in_usd')->nullable();
            $table->string       ('exchange_rate')->nullable();
            $table->string       ('total_value_usd_exchange')->nullable();
            $table->string       ('unit_price_in_inr')->nullable();
            $table->string       ('assess_value_in_inr')->nullable();
            $table->string       ('duty')->nullable();
            $table->string       ('cha_number')->nullable();
            $table->string       ('cha_name')->nullable();
            $table->string       ('invoice_no')->nullable();
            $table->string       ('item_number')->nullable();
            $table->string       ('be_type')->nullable();
            $table->string       ('a_group')->nullable();
            $table->string       ('indian_port')->nullable();
            $table->string       ('cush')->nullable();
            $table->bigInteger   ('created_by')->nullable();
            $table->bigInteger   ('updated_by')->nullable();
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
        Schema::dropIfExists('importer_bills');
    }
}
