<?php

namespace App\Actions;

use App\Models\TransactionHistory;
use App\Models\WalletTransaction;
use Exception;

class TransactionAction
{
    public function execute($fromUser, $toUser, $amount)
    {
        if ($fromUser->wallet_balance < $amount) {
            throw new Exception('Insufficient balance');
        }
        if (!$toUser) {
            throw new Exception('User not found');
        }
        $toUser->wallet_balance += $amount;
        $toUser->save();
        $fromUser->wallet_balance -= $amount;
        $fromUser->save();

        TransactionHistory::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'amount' => $amount,
        ]);
    }
}