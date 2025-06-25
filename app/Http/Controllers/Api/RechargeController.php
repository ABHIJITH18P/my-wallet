<?php

namespace App\Http\Controllers\Api;

use App\Actions\RechargeAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController;
use App\Models\RechargeHistory;
use App\Models\User;
use App\Services\PinValidationService;

class RechargeController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $response = RechargeHistory::select('id', 'amount', 'created_at')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->sendResponse($response, 'Recharge history fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('Recharge failed', [$e->getMessage()]);
        }
    }

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
            return $this->sendResponse($data, 'balance fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('balance fetch failed', [$e->getMessage()]);
        }
    }

    public function store(Request $request, RechargeAction $rechargeAction, PinValidationService $pinValidationService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'pin' => 'required|integer|digits:6',
        ]);
        try {
            DB::beginTransaction();
            $user = $request->user();
            $pinValidationService->validate($request->pin, $user->pin);
            $rechargeAction->execute($request->user(), $request->amount);
            DB::commit();
            $response = [
                'new_balance' => $user->wallet_balance,
            ];
            return $this->sendResponse($response, 'Recharge successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Recharge failed', [$e->getMessage()]);
        }
    }
}
