<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportBillsFile extends Model
{
    protected $table = 'export_bills_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    
}
