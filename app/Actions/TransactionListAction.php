<?php

namespace App\Actions;

use App\Models\WalletHistory;
use App\Models\User;
use App\Models\WalletTransaction;

class TransactionListAction
{
    public function execute($userId)
    {
        
        $recharges = WalletHistory::select('id','amount', 'created_at','')
            ->selectRaw("'credit' as transaction_type")
            ->selectRaw("'recharge' as type")
            ->selectRaw("'' as toUserName")
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');
        // $transactions = WalletTransaction::select('id', 'amount', 'created_at')
        //     ->where('from_user_id', $userId)
        
    }
}