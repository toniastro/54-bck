<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanOfferRequest;
use App\Http\Requests\CreateLoanRquest;
use App\Http\Requests\LoanOfferRequest;
use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * @var LoanService
     */
    private $service;

    public function __construct(LoanService $service)
    {
        $this->service = $service;
    }

    public function getLoans()
    {
        return $this->service->getOpenLoans();
    }

    public function createLoan(CreateLoanRquest $request, $userID)
    {
        $payload= $request->validated();
        return $this->service->requestLoan($payload, $userID);
    }
    public function createLoanOffer(CreateLoanOfferRequest $request, $userID)
    {
        $payload= $request->validated();
        return $this->service->createLoanOffer($payload, $userID);
    }

    public function acceptLoanOffer(LoanOfferRequest $request, $userID)
    {
        $payload= $request->validated();
        return $this->service->acceptLoanOffer($payload, $userID);
    }

    public function declineLoanOffer(LoanOfferRequest $request, $userID)
    {
        $payload= $request->validated();
        return $this->service->declineLoanOffer($payload, $userID);
    }
}
