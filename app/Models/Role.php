<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'status', 'order', 'created_by', 'updated_by',
    ];



    public function role_user()
    {
        return $this->belongsTo('App\User','role_id','id');
    }

}
