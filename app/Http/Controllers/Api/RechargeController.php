<?php

namespace App\Http\Controllers\Api;

use App\Actions\RechargeAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController;

class RechargeController extends BaseController
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
            $data = [
                'new_balance' => $user->wallet_balance,
            ];
            return $this->sendResponse($data, 'Recharge successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Recharge failed', [$e->getMessage()]);
        }
    }
}
