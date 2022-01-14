<?php

namespace App\Repositories;

use App\Repositories\Interfaces\WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface {

    public function updateWalletBalance($payload, $user)
    {
        $user->wallet->total_balance += $payload['amount'];
        $user->wallet->available_balance += $payload['amount'];
        $user->wallet->save();
        return $user;
    }
}
