<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'to_user_id','id');
    }
    
}
