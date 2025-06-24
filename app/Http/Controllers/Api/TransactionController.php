<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\TransactionAction;
use App\Actions\TransactionListAction;

class TransactionController extends BaseController
{
    public function index(Request $request, TransactionListAction $transactionListAction)
    {
        try {
            $response = $transactionListAction->execute($request->user()->id);
            return $this->sendResponse($response, 'Transactions retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to get transactions list', [$e->getMessage()]);
        }
    }

    public function store(Request $request, TransactionAction $transactionAction)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:1|max:10000',
            ]);
            $fromUser = $request->user();
            $toUser = User::find($request->user_id);
            $response = $transactionAction->execute($fromUser, $toUser, $request->amount);
            return $this->sendResponse($response, 'Transaction successful');
        } catch (\Exception $e) {
            return $this->sendError('Transaction failed', [$e->getMessage()]);
        }
    }
    
}
