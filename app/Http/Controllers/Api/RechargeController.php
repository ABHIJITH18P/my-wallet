<?php

namespace App\Http\Controllers\Api;

use App\Actions\RechargeAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RechargeController extends Controller
{
    public function store(Request $request, RechargeAction $rechargeAction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);
        try{
            DB::beginTransaction();
            $user = $request->user();
            $rechargeAction->execute($request->user(), $request->amount);
            DB::commit();
            return response()->json([
                'message' => 'Recharge successful',
                'new_balance' => $user->wallet_balance,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Recharge failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
