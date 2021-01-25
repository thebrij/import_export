<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpChartDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exp_chart_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('api_id');
            $table->string('exporter')->nullable();
            $table->year('year')->nullable();
            $table->string('labeltitle')->nullable();
            $table->string('labelvalue')->nullable();
            $table->string('foreign_country')->nullable();
            $table->string('top15_usd')->nullable();
            $table->string('week_start')->nullable();
            $table->string('week_end')->nullable();
            $table->string('top_quantity')->nullable();
            $table->string('consinee')->nullable();
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
        Schema::dropIfExists('exp_chart_data');
    }
}
