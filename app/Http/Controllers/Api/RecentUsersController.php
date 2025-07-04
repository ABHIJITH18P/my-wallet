<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Actions\GetRecentUserDetailAction;

class RecentUsersController extends BaseController
{
    public function index(Request $request, GetRecentUserDetailAction $getRecentUserDetailAction)
    {
        $request->validate([
            'limit' => 'sometimes',
        ]);
        try {
            $response = $getRecentUserDetailAction->execute($request->user()->id,$request->limit);
            return $this->sendResponse($response, 'recent users retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to get transactions list', [$e->getMessage()]);
        }
    }
    
}
