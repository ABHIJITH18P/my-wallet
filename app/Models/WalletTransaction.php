<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $guarded = [];
    Const TYPE_DEBIT = 'debit';
    Const TYPE_CREDIT = 'credit';
}
