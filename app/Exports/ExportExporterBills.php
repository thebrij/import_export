<?php

namespace App\Exports;

use App\Models\ExporterBill;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportExporterBills implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return ExporterBill::all();
        return ExporterBill::get();
    }
}
