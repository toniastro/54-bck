<?php

namespace App\Services;

use App\Notifications\WelcomeNewUserNotification;
use App\Repositories\Interfaces\RegisterRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegisterService{

    /**
     * @var RegisterRepositoryInterface
     */
    private $repository;

    public function __construct(RegisterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function register(array $payload)
    {
        DB::beginTransaction();
        try{
            $user = $this->repository->register($payload);
        }catch (\Exception $e){
            report($e->getMessage());
            return null;
        }
        DB::commit();
        $user->notify(new WelcomeNewUserNotification());
        return $user;
    }
}
