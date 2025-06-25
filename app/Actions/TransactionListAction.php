<?php

namespace App\Actions;

use App\Models\TransactionHistory;
use App\Models\User;

class TransactionListAction
{
    public function execute($fromUserId, $toUserId, $fromDate, $toDate, $search)
    {
        $usersIds = User::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->pluck('id');
        $data = TransactionHistory::select('transaction_histories.id', 'transaction_histories.created_at', 'amount', 'to_user_id', 'from_user_id')
            ->leftJoin('users as from_users', 'transaction_histories.from_user_id', 'from_users.id')
            ->leftJoin('users as to_users', 'transaction_histories.to_user_id',  'to_users.id',)

            ->orderBy('created_at', 'desc')
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('created_at', '<=', $toDate);
            })
            ->when($toUserId, function ($query) use ($toUserId) {
                return $query->where('to_user_id', $toUserId);
            })
            ->when($search, function ($query) use ($usersIds, $fromUserId) {
                return $query->where(function ($subQuery) use ($usersIds, $fromUserId) {
                    $subQuery->whereIn('to_user_id', $usersIds)
                        ->where('from_user_id', $fromUserId);
                })->orWhere(function ($subQuery) use ($usersIds, $fromUserId) {
                    $subQuery->where('to_user_id', $fromUserId)
                        ->whereIn('from_user_id', $usersIds);
                });
            }, function ($query) use ($fromUserId, $toUserId) {
                if (!$toUserId)
                    return $query->where('from_user_id', $fromUserId)
                        ->orWhere('to_user_id', $fromUserId);
            })
            ->get();
        $fromUsers = User::select('id', 'name', 'phone')->whereIn('id', $data->pluck('from_user_id')->unique())
            ->get();
        $toUsers = User::select('id', 'name', 'phone')->whereIn('id', $data->pluck('to_user_id')->unique())
            ->get();
        $data->transform(function ($item) use ($fromUserId, $fromUsers, $toUsers, $toUserId) {
            if ($item['to_user_id'] != $fromUserId) {
                $item['amount'] = $item['amount'] * -1;
                $item['user'] =  $fromUsers->select('id', 'name', 'phone')->where('id', $item['from_user_id'])->first();
            } else {
                $item['user'] =  $toUsers->select('id', 'name', 'phone')->where('id', $item['to_user_id'])->first();
            }
            unset($item['from_user_id']);
            unset($item['to_user_id']);
            return $item;
        });
        return $data;
    }
}
