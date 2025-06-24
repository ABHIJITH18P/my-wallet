<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\GetAllUsersAction;
use App\Models\User;

class UserController extends Controller
{
    public function index(GetAllUsersAction $getAllUsersAction)
    {
        try {
            $users = $getAllUsersAction->execute();
            return response()->json([
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            return response()->json([
                'message' => 'User retrieved successfully',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
