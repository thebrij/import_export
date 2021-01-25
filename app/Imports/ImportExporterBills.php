<?php

namespace App\Imports;

use App\User;
use App\Models\ExporterBill;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
Use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use \Carbon\Carbon;

class ImportExporterBills implements ToModel, WithChunkReading, ShouldQueue , WithStartRow, WithEvents, WithBatchInserts
{
    use Importable;
    public function __construct(User $importedBy)
    {
        $this->created_by = $importedBy;
    }
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
        return new ExporterBill([
            //
            'shipping_bill_no'         => $row[0],
            'shipping_bill_date'       => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]))->toDateTimeString(),
            'iec'                      => $row[2],
            'exporter'                 => $row[3],
            'exporter_address_n_city'  => $row[4],
            'city'                     => $row[5],
            'pin'                      => $row[6],
            'state'                    => $row[7],
            'contact_no'               => $row[8],
            'e_mail_id'                => $row[9],
            'consinee'                 => $row[10],
            'consinee_address'         => $row[11],
            'port_code'                => $row[12],
            'foreign_port'             => $row[13],
            'foreign_country'          => $row[14],
            'hs_code'                  => $row[15],
            'chapter'                  => $row[16],
            'product_descripition'     => $row[17],
            'quantity'                 => $row[18],
            'unit_quantity'            => $row[19],
            'item_rate_in_fc'          => $row[20],
            'currency'                 => $row[21],
            'total_value_in_usd'       => $row[22],
            'unit_rate_in_usd_exchange' => $row[23],
            'exchange_rate_usd'        => $row[24],
            'total_value_in_usd_exchange' => $row[25],
            'unit_value_in_inr'        => $row[26],
            'fob_in_inr'               => $row[27],
            'invoice_serial_number'    => $row[28],
            'invoice_number'           => $row[29],
            'item_number'              => $row[30],
            'drawback'                 => $row[31],
            'month'                    => $row[32],
            'year'                     => $row[33],
            'mode'                     => $row[34],
            'indian_port'              => $row[35],
            'cush'                     => $row[36],
            'file_name'           => $_FILES['file']['name'],
            'created_by'          => $this->created_by,
            'updated_by'          => $this->created_by,
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
