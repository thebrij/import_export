<?php

namespace App\Console\Commands;

use App\Models\ImportBillsFile;
use App\Models\ImporterBill;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class ProcessPendingImportBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:bills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var string
     */
    protected $ferrormsg = '';

    /**
     * @var bool
     */
    protected $ferror = false;

    /**
     * Create a new command instance.
     *
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
                $importImporters = (new FastExcel)->import(storage_path('app/'.$file->filepath), function ($row) {
                    $this->output->title('Loop row');
                    //if(!isset($row['Bill of Entry Date'])){ $this->ferror = true; $this->ferrormsg = 'Column "Bill of Entry Date" not Found in Excel file';       }
                    //else {
                           return ImporterBill::updateOrCreate([
                                'bill_of_entry_no' => $row['Bill of Entry No'],
                                'bill_of_entry_date' => $row['Bill of Entry Date']->format('Y-m-d'),
                                'importer_id' => $row['Importer ID'],
                                'importer_name' => $row['Importer_Name'],
                                'importer_address' => $row['Importer_Address'],
                                'importer_city_state' => $row['Importer_City_State'],
                                'pin_code' => $row['PIN Code'],
                                'city' => $row['City '],
                                'state' => $row['State'],
                                'contact_no' => $row['Contact No'],
                                'email' => $row['Email'],
                                'supplier' => $row['Supplier'],
                                'supplier_address' => $row['Supplier Address'],
                                'foreign_port' => $row['Foreign Port'],
                                'origin_country' => $row['Origin Country'],
                                'hs_code' => $row['HS Code'],
                                'chapter' => $row['Chapter'],
                                'product_discription' => $row['Product Discription'],
                                'quantity' => $row['Quantity'],
                                'unit_quantity' => $row['Unit Quantity'],
                                'unit_value_as_per_invoice' => $row['Unit value as per Invoice'],
                                'invoice_currency' => $row['INVOICE_CURRENCY'],
                                'total_value_in_fc' => $row['Total Value In FC'],
                                'unit_rate_in_usd' => $row['Unit Rate in USD'],
                                'exchange_rate' => $row['Exchange Rate'],
                                'total_value_usd_exchange' => $row['Total Value USD (Exchange'],
                                'unit_price_in_inr' => $row['Unit_Price in INR'],
                                'assess_value_in_inr' => $row['Assess_Value_In_INR'],
                                'duty' => $row['Duty'],
                                'cha_number' => $row['CHA Number'],
                                'cha_name' => $row['CHA_Name'],
                                'invoice_no' => $row['Invoice No'],
                                'item_number' => $row['Item Number'],
                                'be_type' => $row['BE_Type'],
                                'a_group' => $row['A_Group'],
                                'indian_port' => $row['INDIAN Port'],
                                'cush' => $row['CUSH'],
                                //'file_name' => $file->file_name,
                                //'created_by' => $file->user_id,
                                //'updated_by' => $file->user_id,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]);
                        //}
                    //return false;
                });
                $this->output->success('Import successfully completed'.$file->id);
                ImportBillsFile::where('id', $file->id)->update(['status' => 2]);
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
