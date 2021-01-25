<?php

namespace App\Exports;

use App\Models\ImporterBill;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportImporterBills implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return ImporterBill::all();
        return ImporterBill::get();
    }
}
