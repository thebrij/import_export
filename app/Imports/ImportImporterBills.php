<?php

namespace App\Imports;

use App\Models\ImporterBill;
use Maatwebsite\Excel\Concerns\ToModel;
Use Maatwebsite\Excel\Concerns\WithStartRow;
use \Carbon\Carbon;

class ImportImporterBills implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ImporterBill([
            //
            'bill_of_entry_no'    => $row[0],
            'bill_of_entry_date'  => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1])),
            'importer_id'         => $row[2],
            'importer_name'       => $row[3],
            'importer_address'    => $row[4],
            'importer_city_state' => $row[5],
            'pin_code'            => $row[6],
            'city'                => $row[7],
            'state'               => $row[8],
            'contact_no'          => $row[9],
            'email'               => $row[0],
            'supplier'            => $row[11],
            'supplier_address'    => $row[12],
            'foreign_port'        => $row[13],
            'origin_country'      => $row[14],
            'hs_code'             => $row[15],
            'chapter'             => $row[16],
            'product_discription' => $row[17],
            'quantity'            => $row[18],
            'unit_quantity'       => $row[19],
            'unit_value_as_per_invoice' => $row[20],
            'invoice_currency'    => $row[21],
            'total_value_in_fc'   => $row[22],
            'unit_rate_in_usd'    => $row[23],
            'exchange_rate'       => $row[24],
            'total_value_usd_exchange' => $row[25],
            'unit_price_in_inr'   => $row[26],
            'assess_value_in_inr' => $row[27],
            'duty'                => $row[28],
            'cha_number'          => $row[29],
            'cha_name'            => $row[30],
            'invoice_no'          => $row[31],
            'item_number'         => $row[32],
            'be_type'             => $row[33],
            'a_group'             => $row[34],
            'indian_port'         => $row[35],
            'cush'                => $row[36],
            'file_name'           => $_FILES['file']['name'],
            'created_by'          => \Auth::user()->id,
            'updated_by'          => \Auth::user()->id,
            'created_at'          => Carbon::now()->toDateTimeString(),
            'updated_at'          => Carbon::now()->toDateTimeString(),
        ]);
    }
    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function(ImportFailed $event) {
                $this->created_by->notify(new ImportHasFailedNotification);
            },
        ];
    }

    public function batchSize(): int
    {
        return 400;
    }

    public function chunkSize(): int
    {
        return 400;
    }
}
