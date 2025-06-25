<?php

namespace App\Http\Controllers\Api;

use App\Actions\RechargeAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Services\PinValidationService;

class RechargeController extends BaseController
{
    public function show(Request $request, $id, PinValidationService $pinValidationService)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'pin' => 'required|integer|digits:6',
        ]);
        try {
            $user = User::findOrFail($id);
            $pinValidationService->validate($request->pin, $user->pin);
            $data = [
                'balance' => $user->wallet_balance,
            ];
            return $this->sendResponse($data, 'balance fetched successful');
        } catch (\Exception $e) {
            return $this->sendError('balance fetch failed', [$e->getMessage()]);
        }
    }
    
    public function store(Request $request, RechargeAction $rechargeAction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);
        try {
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
