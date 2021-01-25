<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string       ('name')->unique();
            $table->string       ('description')->nullable();
            $table->string       ('status')->nullable();
            $table->bigInteger   ('order')->nullable();
            $table->integer      ('created_by')->unsigned();
            $table->integer      ('updated_by')->unsigned();

            //$table->foreign      ('created_by')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign      ('updated_by')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('roles');
    }
}
