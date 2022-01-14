<?php

namespace App\Repositories\Interfaces;

interface LoanRepositoryInterface{

    public function createLoanRequest(array $payload, $userID);

    public function createLoanOffer(array $payload, $userID);

    public function acceptLoanOffer(array $payload, $userID);

    public function declineLoanOffer(array $payload, $userID);
}
