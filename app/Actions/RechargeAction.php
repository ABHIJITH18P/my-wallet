<?php

namespace App\Actions;

use App\Models\RechargeHistory;

class RechargeAction
{
    public function execute($user, $amount)
    {
        
        $user->wallet_balance += $amount;
        $user->save();

        RechargeHistory::create([
            'user_id' => $user->id,
            'amount'  => $amount,
        ]);
    }
}