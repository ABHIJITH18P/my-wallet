<?php

namespace App\Actions;

use App\Models\TransactionHistory;

class GetRecentUserDetailAction
{
    public function execute($userId, $limit)
    {
        $uniqueUsers = [];
        $data = TransactionHistory::select('from_user_id', 'to_user_id', 'from_users.name as from_user_name', 'from_users.phone as from_user_phone', 'to_users.name as to_user_name', 'to_users.phone as to_user_phone', 'transaction_histories.created_at')
            ->where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->leftJoin('users as from_users', 'transaction_histories.from_user_id', 'from_users.id')
            ->leftJoin('users as to_users', 'transaction_histories.to_user_id', 'to_users.id')
            ->orderBy('transaction_histories.created_at', 'desc')
            ->get()
            ->map(function ($item) use ($userId, &$uniqueUsers) {
                if ($item->from_user_id != $userId) {
                    $item->id = $item->from_user_id;
                    $item->name = $item->from_user_name;
                    $item->phone = $item->from_user_phone;
                } else {
                    $item->id = $item->to_user_id;
                    $item->name = $item->to_user_name;
                    $item->phone = $item->to_user_phone;
                }

                unset($item->from_user_name, $item->from_user_phone, $item->to_user_name, $item->to_user_phone, $item->from_user_id, $item->to_user_id);

                // Check if user ID already exists in the uniqueUsers array
                if (!isset($uniqueUsers[$item->id])) {
                    $uniqueUsers[$item->id] = $item; // Add user to uniqueUsers
                    return $item;
                }

                return null;
            })
            ->filter();
        if ($limit)
            $data = $data->take($limit);

        return $data->values()->toArray();
    }
}
