<?php

namespace App\Console\Commands;

use App\Models\ImportBillsFile;
use App\Models\ImporterBill;
use App\Models\TempData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportImporterBills;
use Auth;

class ImportBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importbill:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        \Log::info("data import starting");
        $isInProgressFile = ImportBillsFile::where('status', 1)->pluck('id');

        if (isset($isInProgressFile[0])) {
            return true;
        }

        $file = ImportBillsFile::where('status', 0)->limit(1)->get();
        
        if (isset($file[0])) {
            $file = $file[0];
            //update status of file from pending to in progress
            $file->where('id', $file->id)->update(['status' => 1]);
            $this->output->title('Starting import');
            try {
                $this->ferror = false;
                // working code 

                $filePath = storage_path('app/'.$file->filepath);
                $file_path = str_replace('\\','/',$filePath);
                
                $file_path = str_replace($_SERVER['DOCUMENT_ROOT'],'',$file_path);
                $im = '"';
                $file_name  = $file->filename;
                $created_by = $file->user_id;
                $updated_by = $file->user_id;
                
                \DB::connection()->getPdo()
                    ->exec("LOAD DATA LOCAL INFILE '{$file_path}'
                            INTO TABLE importer_bills
                            FIELDS TERMINATED BY ',' 
                            ENCLOSED BY '$im'
                            LINES TERMINATED BY '\n'
                            IGNORE 1 LINES
                            (bill_of_entry_no, bill_of_entry_date, importer_id,importer_name,importer_address,importer_city_state,pin_code,city,state,contact_no,email,supplier,supplier_address,foreign_port,origin_country,hs_code,chapter,product_discription,quantity,unit_quantity,unit_value_as_per_invoice,invoice_currency,total_value_in_fc,unit_rate_in_usd,exchange_rate,total_value_usd_exchange,unit_price_in_inr,	assess_value_in_inr,duty,cha_number,cha_name,invoice_no,item_number,be_type,a_group,indian_port,cush)
                            SET id = NULL, 
                                file_name  = '$file_name',
                                created_by = '$created_by',
                                updated_by = '$updated_by',
                                created_at = NOW(),
                                updated_at = NOW()
                            ");


                // working code 
                
                \Log::info("data import successfully");
                $this->output->success('Import successfully completed'.$file->id);
                ImportBillsFile::where('id', $file->id)->update(['status' => 2]);
                $this->output->title('Updated record');
                $path = storage_path('app/'.$file->filepath);
                unlink($path);
                // store data for charts
                    // import datashboard 
                    // get_ajax_top_usd
                    // $get_ajax_top_usd = DB::select('select `importer_name`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` group by `importer_name` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0');
                    // $TempData = TempData::find(1);
                    // $TempData->get_ajax_top_usd = json_encode($get_ajax_top_usd);
                    // $TempData->save();
                    
                    // // get_ajax_top_usd_port
                    // $get_ajax_top_usd_port = DB::select('select indian_port AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2)  AS labelvalue from `importer_bills` group by `indian_port` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0');
                    // $TempData = TempData::find(1);
                    // $TempData->get_ajax_top_usd_port = json_encode($get_ajax_top_usd_port);
                    // $TempData->save();

                    // // get_ajax_top_usd_country
                    // $get_ajax_top_usd_country = DB::select('select `origin_country`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` group by `origin_country` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0');
                    // $TempData = TempData::find(1);
                    // $TempData->get_ajax_top_usd_country = json_encode($get_ajax_top_usd_country);
                    // $TempData->save();

                return true;
            } catch (\Exception $exception) {
                \Log::info($exception->getMessage());
                //update status of file to pending from in progress to get it process again
                ImportBillsFile::where('id', $file->id)->update(['status' => 0]);
                echo $exception->getMessage();
                return false;
            }
        }
        return true;
    
    }
}
