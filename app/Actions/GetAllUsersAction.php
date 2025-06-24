<?php

namespace App\Actions;

use App\Models\WalletHistory;
use App\Models\User;

class GetAllUsersAction
{
    public function execute()
    {
        
        return User::all();
    }
}