<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    /**
     * @var RegisterService
     */
    private $service;

    public function __construct(RegisterService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request)
    {
        $payload = $request->validated();
        $user = $this->service->register($payload);
        if($user){
            return $this->gene_response(true, 'Account created successfully', new UserResource($user));
        }
        return $this->gene_response(false, 'Something went wrong creating account');
    }
}
