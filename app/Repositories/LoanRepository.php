<?php

namespace App\Repositories;

use App\Exceptions\ExpiredLoanOffer;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\InvalidWallet;
use App\Models\LoanOffers;
use App\Models\Loans;
use App\Models\Wallet;
use App\Notifications\LoanOfferAcceptedNotification;
use App\Notifications\LoanOfferCreatedNotification;
use App\Notifications\LoanOfferRejectedNotification;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanRepository implements LoanRepositoryInterface{

    public function createLoanRequest(array $payload, $userID)
    {
        $loan = new Loans();
        $loan->amount = $payload['amount'];
        $loan->reason = $payload['reason'];
        $loan->borrower_id = $userID;
        $loan->save();
        return $loan->fresh();
    }

    public function createLoanOffer(array $payload, $userID)
    {
        $loan_offers = new LoanOffers();
        $loan_offers->loan_id = $payload['loan_id'];
        $loan_offers->lender_id = $userID;
        $loan_offers->interest_rate = $payload['interest_rate'];
        $loan_offers->maturity_date = $payload['maturity_date'];
        $loan_offers->save();
        $loan_offers->loans->borrower->notify(new LoanOfferCreatedNotification());
        return $loan_offers->fresh();
    }

    /**
     * @throws \Exception
     */
    public function acceptLoanOffer(array $payload, $userID)
    {
        $loan_offers = LoanOffers::lockForUpdate()->where('id',$payload['loan_offer_id'])->where('status',LoanOffers::$open)->first();

        //check that the offer exists and that the user requesting is the owner of the loan
        if(!$loan_offers || $loan_offers->loans->borrower_id != $userID ) {
            throw new ExpiredLoanOffer('Loan offer does not exists or is no longer open');
        }

        //Check that the loan is still available to be accepted
        if($loan_offers->loans->granted) {
            throw new ExpiredLoanOffer('Loan has been accepted by another offer');
        }

        //Update LoanOffer Status

        $loan_offers->status = LoanOffers::$accepted;
        $loan_offers->loans->granted = true;
        $loan_offers->loans->lender_id = $userID;
        $loan_offers->push();
        $lender_wallet = Wallet::where('user_id', $loan_offers->lender_id)->first();
        if($lender_wallet){
            if($lender_wallet->available_balance < $loan_offers->loans->amount){
                throw new InsufficientBalance('Lender does not have enough balance in wallet to make this loan');
            }
            //Debit Lender
            $lender_wallet->available_balance -= $loan_offers->loans->amount;
            $lender_wallet->total_balance -= $loan_offers->loans->amount;
            $lender_wallet->update();

            //Credit Borrower
            $borrower_wallet = Wallet::where('user_id',$userID)->first();
            if($borrower_wallet){
                $borrower_wallet->available_balance += $loan_offers->loans->amount;
                $borrower_wallet->total_balance += $loan_offers->loans->amount;
                $borrower_wallet->update();
                $loan_offers->loans->lender->notify(new LoanOfferAcceptedNotification());
                return true;
            }
            throw new InvalidWallet('Borrower Wallet not found');
        }
        throw new InvalidWallet('Lender Wallet not found');
    }

    /**
     * @throws ExpiredLoanOffer
     */
    public function declineLoanOffer(array $payload, $userID)
    {
        $loan_offers =  LoanOffers::lockForUpdate()->where('id',$payload['loan_offer_id'])->where('status',LoanOffers::$open)->first();

        //check that the offer exists and that the user requesting is the owner of the loan
        if($loan_offers && $loan_offers->loans->borrower_id == $userID){
            $loan_offers->status = LoanOffers::$rejected;
            $loan_offers->update();
            $loan_offers->loans->lender->notify(new LoanOfferRejectedNotification());
            return true;
        }
        throw new ExpiredLoanOffer('Loan offer does not exists or is no longer open');
    }
}
