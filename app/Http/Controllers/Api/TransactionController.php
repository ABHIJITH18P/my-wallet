<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\TransactionAction;
use App\Actions\TransactionListAction;
use App\Models\TransactionHistory;
use App\Services\PinValidationService;
use Illuminate\Support\Facades\DB;

class TransactionController extends BaseController
{
    public function index(Request $request, TransactionListAction $transactionListAction)
    {
        $request->validate([
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date',
            'to_user_id' => 'sometimes|exists:users,id',
            'search' => 'sometimes|max:255',
        ]);
        try {
            $response = $transactionListAction->execute($request->user()->id,$request->to_user_id, $request->from_date, $request->to_date, $request->search);
            return $this->sendResponse($response, 'Transactions retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to get transactions list', [$e->getMessage()]);
        }
    }

    public function show(Request $request,$id)
    {
        $request->merge([
            'id' => $id,
        ]);
        $request->validate([
            'id' => 'required|integer|exists:transaction_histories,id',
        ]);
        try {
            $response = TransactionHistory::select('id', 'amount', 'created_at', 'to_user_id','from_user_id')
                ->where('id', $request->id)
                ->with(['user' => function ($query) {
                    $query->select('id', 'name', 'phone');
                }])
                ->first();
            if($response['from_user_id'] == $request->user()->id) {
                $response['amount'] = $response['amount'] * -1; 
            }
            unset($response['to_user_id']);
            return $this->sendResponse($response, 'Transaction retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to get transactions list', [$e->getMessage()]);
        }
    }

    public function store(Request $request, TransactionAction $transactionAction, PinValidationService $pinValidationService)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'to_user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:1|max:50000',
                'pin' => 'required|integer|digits:6',
            ]);
            $fromUser = $request->user();
            $pinValidationService->validate($request->pin, $fromUser->pin);
            $toUser = User::find($request->to_user_id);
            $response = $transactionAction->execute($fromUser, $toUser, $request->amount);
            DB::commit();
            return $this->sendResponse($response, 'Transaction successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Transaction failed', [$e->getMessage()]);
        }
    }
    
}
