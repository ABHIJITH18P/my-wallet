<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetAllUsersAction;
use App\Models\User;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function index(GetAllUsersAction $getAllUsersAction)
    {
        try {
            $users = $getAllUsersAction->execute();
            return $this->sendResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve users', [$e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            return $this->sendResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve user', [$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $response = User::where('id', $request->user()->id)
                ->select('id', 'name', 'phone')
                ->first();
            $response['is_pin_set'] = $request->user()->pin ? true : false;
            return $this->sendResponse($response, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve user', [$e->getMessage()]);
        }
    }
}
