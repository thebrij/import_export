<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportBillsFile extends Model
{
    protected $table = 'import_bills_file';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
