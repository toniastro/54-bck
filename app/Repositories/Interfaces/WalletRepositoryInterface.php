<?php

namespace App\Repositories\Interfaces;

interface WalletRepositoryInterface{

    public function updateWalletBalance($payload, $user);

}
