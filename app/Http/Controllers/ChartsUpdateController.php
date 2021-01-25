<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpChartData;
use App\Models\ExporterBill;

use App\Models\ImporterBill;
use App\Models\ChartData;
use App\Models\ImportBillsFile;



use Illuminate\Support\Facades\DB;
class ChartsUpdateController extends Controller
{
    public function expUpdateChart()
    {
    
    
            ExpChartData::truncate();
           $exporters_top_usd = ExporterBill::select('exporter','year',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
          ->groupBy('year','exporter')
          ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
          ->limit(15)->get()->toArray();
            foreach ($exporters_top_usd as $key => $data) {
            $exporters_top_usd[$key]['api_id'] = 1; 
            }
            ExpChartData::insert($exporters_top_usd);


            // get_ajax_top_usd_port api 2
            $topusdport = ExporterBill::select('year',DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year','indian_port')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($topusdport as $key => $data) {
            $topusdport[$key]['api_id'] = 2; 
            }
            ExpChartData::insert($topusdport);
          

            // get_ajax_top_usd_country api 3
          $topusdcountry = ExporterBill::select('year','foreign_country',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
                ->groupBy('year','foreign_country')
                ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
                ->limit(15)->get()->toArray();
            foreach ($topusdcountry as $key => $data) {
             $topusdcountry[$key]['api_id'] = 3; 
            }
           
            ExpChartData::insert($topusdcountry);
            
            // ga_marketshare_cost_usd_port api 4
            $marketshare_cost_usd_country2 = ExporterBill::select('year',DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year','indian_port')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($marketshare_cost_usd_country2 as $key => $data) {
            $marketshare_cost_usd_country2[$key]['api_id'] = 4; 
            }
            ExpChartData::insert($marketshare_cost_usd_country2);



            // ga_marketshare_cost_qua_port api 5 
            $marketshare_cost_qua_port2 = ExporterBill::select('year',DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
            ->groupBy('year','indian_port')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($marketshare_cost_qua_port2 as $key => $data) {
            $marketshare_cost_qua_port2[$key]['api_id'] = 5; 
            }
            ExpChartData::insert($marketshare_cost_qua_port2);


            // ga_marketshare_cost_qua_country api 6
            $marketshare_cost_qua_country2 = ExporterBill::select('year',DB::raw('foreign_country AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
            ->groupBy('year','foreign_country')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($marketshare_cost_qua_country2 as $key => $data) {
            $marketshare_cost_qua_country2[$key]['api_id'] = 6; 
            }
            ExpChartData::insert($marketshare_cost_qua_country2);


            // ga_marketshare_cost_usd_country api 7
            $marketshare_cost_usd_country2 = ExporterBill::select('year',DB::raw('foreign_country AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year','exporter')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($marketshare_cost_usd_country2 as $key => $data) {
            $marketshare_cost_usd_country2[$key]['api_id'] = 7; 
            }
            ExpChartData::insert($marketshare_cost_usd_country2);



            // ga_priceana_usd_country api 8 
            $priceana_usd_country = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('foreign_country AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();

            foreach ($priceana_usd_country as $key => $data) {
            $priceana_usd_country[$key]['api_id'] = 8; 
            }
            ExpChartData::insert($priceana_usd_country);


            // ga_priceana_usd_port api 9 
            $priceana_usd_port = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('indian_port AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();
            foreach ($priceana_usd_port as $key => $data) {
            $priceana_usd_port[$key]['api_id'] = 9; 
            }
            ExpChartData::insert($priceana_usd_port);

            // ga_priceana_usd_exporter api 10 
            $priceana_usd_exporter = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('exporter AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('exporter'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();
            foreach ($priceana_usd_exporter as $key => $data) {
            $priceana_usd_exporter[$key]['api_id'] = 10; 
            }
            ExpChartData::insert($priceana_usd_exporter);


            // ga_comparison_usd_exporter api 11
            $priceana_usd_exporter = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('exporter AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('exporter'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();
            foreach ($priceana_usd_exporter as $key => $data) {
            $priceana_usd_exporter[$key]['api_id'] = 11; 
            }
            ExpChartData::insert($priceana_usd_exporter);


            // ga_comparison_usd_country api 12 
            $priceana_usd_country = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('foreign_country AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();
            foreach ($priceana_usd_country as $key => $data) {
            $priceana_usd_country[$key]['api_id'] = 12; 
            }
            ExpChartData::insert($priceana_usd_country);


            // ga_comparison_usd_port api 13
            $priceana_usd_port = ExporterBill::select('year',DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('indian_port AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
            ->orderBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
            ->limit(100)->get()->toArray();
            foreach ($priceana_usd_port as $key => $data) {
            $priceana_usd_port[$key]['api_id'] = 13; 
            }
            ExpChartData::insert($priceana_usd_port);


            // ga_pc_usd_country_max api 14
            $pc_usd_country_max = ExporterBill::select('year',DB::raw('foreign_country AS labeltitle'), DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')    
            ->groupBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2)'), 'DESC')
            ->limit(15)->get()->toArray();

            foreach ($pc_usd_country_max as $key => $data) {
            $pc_usd_country_max[$key]['api_id'] = 14; 
            }
            ExpChartData::insert($pc_usd_country_max);

            // ga_pc_qua_country_max api 15
            $pc_qua_country_max = ExporterBill::select('year',DB::raw('CONCAT(foreign_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_qua_country_max as $key => $data) {
            $pc_qua_country_max[$key]['api_id'] = 15; 
            }
            ExpChartData::insert($pc_qua_country_max);


            // ga_pc_usd_country_min api 16
            $pc_usd_country_max = ExporterBill::select('year',DB::raw('foreign_country AS labeltitle'), DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_usd_country_max as $key => $data) {
            $pc_usd_country_max[$key]['api_id'] = 16; 
            }
            ExpChartData::insert($pc_usd_country_max);

            // ga_pc_qua_country_min api 17
            $pc_qua_country_min = ExporterBill::select('year',DB::raw('CONCAT(foreign_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_qua_country_min as $key => $data) {
            $pc_qua_country_min[$key]['api_id'] = 17; 
            }
            ExpChartData::insert($pc_qua_country_min);



            // ga_pc_usd_port_max api 18
            $pc_usd_country_max = ExporterBill::select('year',DB::raw('indian_port AS labeltitle'), DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2)'), 'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_usd_country_max as $key => $data) {
            $pc_usd_country_max[$key]['api_id'] = 18; 
            }
            ExpChartData::insert($pc_usd_country_max);


            // ga_pc_qua_port_max api 19
            $pc_qua_country_max = ExporterBill::select('year',DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->groupBy('year')
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();

            foreach ($pc_qua_country_max as $key => $data) {
            $pc_qua_country_max[$key]['api_id'] = 19; 
            }
            ExpChartData::insert($pc_qua_country_max);

            // ga_pc_usd_port_min api 20
            $pc_usd_country_max = ExporterBill::select('year',DB::raw('indian_port AS labeltitle'), DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->groupBy('year')->groupBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_usd_country_max as $key => $data) {
            $pc_usd_country_max[$key]['api_id'] = 20; 
            }
            ExpChartData::insert($pc_usd_country_max);


            // ga_pc_qua_port_min api 21
            $pc_qua_country_min = ExporterBill::select('year',DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->groupBy('year')->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($pc_qua_country_min as $key => $data) {
            $pc_qua_country_min[$key]['api_id'] = 21; 
            }

            ExpChartData::insert($pc_qua_country_min);
            


            // get_ajax_expana_usd_sup api 22
            $expanausdsup = ExporterBill::select('year','exporter',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
            ->groupBy('year')->groupBy('exporter')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($expanausdsup as $key => $data) {
                $expanausdsup[$key]['api_id'] = 22; 
            }
    
            ExpChartData::insert($expanausdsup);
                
    


            // get_ajax_expana_usd_cost api 23

            $expanausdcost = ExporterBill::select('year','exporter',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
            ->groupBy('year')->groupBy('exporter')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
                foreach ($expanausdcost as $key => $data) {
                    $expanausdcost[$key]['api_id'] = 23; 
                }
        
                ExpChartData::insert($expanausdcost);
    


            // get_ajax_expana_usd_quantity api 24
            $expanausdquantity = ExporterBill::select('year','exporter',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
            ->groupBy('year')->groupBy('exporter')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->limit(15)->get()->toArray();
                foreach ($expanausdquantity as $key => $data) {
                    $expanausdquantity[$key]['api_id'] = 24; 
                }
        
                ExpChartData::insert($expanausdquantity);
        

        // ga_exp_conana_usd_sup api 25

        $expanausdsup = ExporterBill::select('year','consinee',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
            ->groupBy('year')->groupBy('consinee')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
        
        foreach ($expanausdsup as $key => $data) {
            $expanausdsup[$key]['api_id'] = 25; 
        }
        ExpChartData::insert($expanausdsup);

        // ga_exp_conana_usd_cost api 26
        $expanausdcost = ExporterBill::select('year','consinee',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
            ->groupBy('year')->groupBy('consinee')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->limit(15)->get()->toArray();
            foreach ($expanausdcost as $key => $data) {
                $expanausdcost[$key]['api_id'] = 26; 
            }
            ExpChartData::insert($expanausdcost);
       
        // ga_exp_conana_usd_quantity api 27 
        $expanausdquantity = ExporterBill::select('year','consinee',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
        ->groupBy('year')->groupBy('consinee')
        ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
        ->limit(15)->get()->toArray();
            foreach ($expanausdquantity as $key => $data) {
                $expanausdquantity[$key]['api_id'] = 27; 
            }
        ExpChartData::insert($expanausdquantity);


            return back()->with('status', __('Export charts data upload successfully'));

    }   
}
