<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAccess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'right_name', 'right_option', 'description', 'created_by', 'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
