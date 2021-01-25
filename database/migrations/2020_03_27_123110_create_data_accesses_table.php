<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger   ('user_id');
            $table->string       ('right_name');
            $table->string       ('right_option');
            $table->string       ('description')->nullable();
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
        Schema::dropIfExists('data_accesses');
    }
}
