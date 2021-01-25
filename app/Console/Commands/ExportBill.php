<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Models\ExportBillsFile;
use App\Models\ExporterBill;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportImporterBills;
use Auth;

class ExportBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exportbill:cron';

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

        
        $isInProgressFile = ExportBillsFile::where('status', 1)->pluck('id');
        
        if (isset($isInProgressFile[0])) {
            return true;
        }
        $file = ExportBillsFile::where('status', 0)->limit(1)->get();
        
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
                            INTO TABLE exporter_bills
                            FIELDS TERMINATED BY ',' 
                            ENCLOSED BY '$im'
                            LINES TERMINATED BY '\n'
                            IGNORE 1 LINES
                            (
                            shipping_bill_no, shipping_bill_date , iec, exporter, exporter_address_n_city,
                            city,pin, state,
                            contact_no,
                            e_mail_id,
                            consinee,consinee_address,
                            port_code,foreign_port,
                            foreign_country,hs_code,
                            chapter,
                            product_descripition,
                            quantity,
                            unit_quantity,
                            item_rate_in_fc,
                            currency,
                            total_value_in_usd,
                            unit_rate_in_usd_exchange,
                            exchange_rate_usd,
                            total_value_in_usd_exchange,
                            unit_value_in_inr,fob_in_inr,invoice_serial_number,
                            invoice_number,item_number,drawback,month,year,mode,indian_port,cush)
                            SET id = NULL, 
                                file_name  = '$file_name',
                                created_by = '$created_by',
                                updated_by = '$updated_by',
                                created_at = NOW(),
                                updated_at = NOW()
                            ");



                // working code 
                
                \Log::info("Export data insert successfully");
                $this->output->success('Export data insert successfully completed'.$file->id);
                ExportBillsFile::where('id', $file->id)->update(['status' => 2]);
                $this->output->title('Updated record');
                $path = storage_path('app/'.$file->filepath);
                unlink($path);
                return true;
            } catch (\Exception $exception) {
                //update status of file to pending from in progress to get it process again
                ImportBillsFile::where('id', $file->id)->update(['status' => 0]);
                echo $exception->getMessage();
                return false;
            }
        }
        return true;
    }
}
