<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpWalletRequest;
use App\Http\Resources\UserResource;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    /**
     * @var WalletService
     */
    private $service;

    public function __construct(WalletService $service)
    {
        $this->service = $service;
    }

    public function update(TopUpWalletRequest $request, $id)
    {
        $payload = $request->validated();
        return $this->service->topLenderWallet($payload, $id);
    }
}
