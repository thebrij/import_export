<?php

use Illuminate\Database\Seeder;
use App\Models\TempData;

class TempDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        TempData::truncate();
        TempData::create([ 'api' => 'imp_get_ajax_top_usd', 'data' => '0']);
        TempData::create([ 'api' => 'imp_get_ajax_top_usd_port', 'data' => '0']);
        TempData::create([ 'api' => 'imp_get_ajax_top_usd_country', 'data' => '0']);
    }
}
