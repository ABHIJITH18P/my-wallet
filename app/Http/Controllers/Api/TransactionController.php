<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\TransactionAction;
use App\Actions\TransactionListAction;

class TransactionController extends Controller
{
    public function index(Request $request, TransactionListAction $transactionListAction)
    {
        try {
            $response = $transactionListAction->execute($request->user()->id);
            return response()->json([
                'message' => 'Transactions retrieved successfully',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to transactions list',
                'error' => $e->getMessage(),
            ], 500);
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
            return response()->json([
                'message' => 'Transaction successful',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Transaction failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
