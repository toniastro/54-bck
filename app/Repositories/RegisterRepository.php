<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\RegisterRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class RegisterRepository implements RegisterRepositoryInterface {

    /**
     * @throws Exception
     */
    public function register(array $payload)
    {
       $user = new User();
       $user->email = $payload['email'];
       $user->name = $payload['name'];
       $user->role_id = $payload['role'];
       $user->save();
       return $user->fresh();
    }
}
