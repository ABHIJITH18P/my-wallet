<?php

namespace App\Actions;

use App\Models\WalletHistory;

class RechargeAction
{
    public function execute($user, $amount)
    {
        
        $user->wallet_balance += $amount;
        $user->save();

        WalletHistory::create([
            'user_id' => $user->id,
            'amount'  => $amount,
        ]);
    }
}