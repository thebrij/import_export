<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExporterBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exporter_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string       ('shipping_bill_no')->nullable();
            $table->date       ('shipping_bill_date')->nullable();
            $table->string       ('iec')->nullable();
            $table->string       ('exporter')->nullable();
            $table->string       ('exporter_address_n_city')->nullable();
            $table->string       ('city')->nullable();
            $table->string       ('pin')->nullable();
            $table->string       ('state')->nullable();
            $table->string       ('contact_no')->nullable();
            $table->string       ('e_mail_id')->nullable();
            $table->string       ('consinee')->nullable();
            $table->string       ('consinee_address')->nullable();
            $table->string       ('port_code')->nullable();
            $table->string       ('foreign_port')->nullable();
            $table->string       ('foreign_country')->nullable();
            $table->string       ('hs_code')->nullable();
            $table->string       ('chapter')->nullable();
            $table->string       ('product_descripition')->nullable();
            $table->string       ('quantity')->nullable();
            $table->string       ('unit_quantity')->nullable();
            $table->string       ('item_rate_in_fc')->nullable();
            $table->string       ('currency')->nullable();
            $table->string       ('total_value_in_usd')->nullable();
            $table->string       ('unit_rate_in_usd_exchange')->nullable();
            $table->string       ('exchange_rate_usd')->nullable();
            $table->string       ('total_value_in_usd_exchange')->nullable();
            $table->string       ('unit_value_in_inr')->nullable();
            $table->string       ('fob_in_inr')->nullable();
            $table->string       ('invoice_serial_number')->nullable();
            $table->string       ('invoice_number')->nullable();
            $table->string       ('item_number')->nullable();
            $table->string       ('drawback')->nullable();
            $table->string       ('month')->nullable();
            $table->string       ('year')->nullable();
            $table->string       ('mode')->nullable();
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
        Schema::dropIfExists('exporter_bills');
    }
}
