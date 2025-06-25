<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\BaseController;

class SetPinNumberController extends BaseController
{
    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|integer|digits:6',
        ]);
        try {
            $user = $request->user();
            $user->pin = Hash::make($request->pin);
            $user->save();
            return $this->sendResponse([], 'Pin number set successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to set pin number', [$e->getMessage()]);
        }
    }
}
