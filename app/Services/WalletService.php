<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\WalletUpdateNotification;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class WalletService{

    use ResponseTrait;

    /**
     * @var WalletRepositoryInterface
     */
    private $repository;

    public function __construct(WalletRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function topLenderWallet(array $payload, $userID)
    {
        //check if user is valid
        $user = User::find($userID);
        if(!$user){
            return $this->gene_response(false, 'Cannot find this user');
        }
        //Only lenders can top up wallet; this user is a borrower
        if($user->role->name != 'lender'){
            return $this->gene_response(false, 'This user does not have a lender role');
        }
        DB::beginTransaction();
        try{
            $user = $this->repository->updateWalletBalance($payload, $user);
        }catch (\Exception $e){
            DB::rollBack();
            report($e->getMessage());
            return $this->gene_response(false, 'Something went wrong updating wallet');
        }
        DB::commit();
        $user->notify(new WalletUpdateNotification());
        return $this->gene_response(true, 'Wallet balance successfully updated', new UserResource($user));
    }
}
