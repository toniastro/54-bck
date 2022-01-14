<?php

namespace App\Services;

use App\Exceptions\ExpiredLoanOffer;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\InvalidWallet;
use App\Http\Resources\LoanCollection;
use App\Http\Resources\LoanOfferResource;
use App\Http\Resources\LoanResource;
use App\Models\Loans;
use App\Models\User;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class LoanService{

    use ResponseTrait;

    /**
     * @var LoanRepositoryInterface
     */
    private $repository;

    public function __construct(LoanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    //For Borrower to create a loan request
    public function requestLoan(array $payload, $userID)
    {
        if(!$this->verifyUser($userID,'borrower')){
            return $this->gene_response(false, 'This user does not exists or is not a borrower');
        }
        DB::beginTransaction();
        try{
            $loan = $this->repository->createLoanRequest($payload, $userID);
        }catch(\Exception $e){
            report($e->getMessage());
            DB::rollBack();
            return $this->gene_response(false, 'Something went wrong creating request');
        }
        DB::commit();
        return $this->gene_response(true, 'Loan request was created successfully', new LoanResource($loan));
    }

    //Lender can create a loan offer to any loan opened by a borrower
    public function createLoanOffer(array $payload, $userID)
    {
        if(!$this->verifyUser($userID,'lender')){
            return $this->gene_response(false, 'This user does not exists or is not a borrower');
        }

        DB::beginTransaction();
        try{
            $loan_offer = $this->repository->createLoanOffer($payload, $userID);
        }catch(\Exception $e){
            report($e->getMessage());
            DB::rollBack();
            return $this->gene_response(false, 'Something went wrong creating offer');
        }
        DB::commit();
        return $this->gene_response(true, 'Loan offer was created successfully', new LoanOfferResource($loan_offer));
    }

    //Borrower can accept a loan offer by any lender
    public function acceptLoanOffer(array $payload, $userID)
    {
        if(!$this->verifyUser($userID,'borrower')){
            return $this->gene_response(false, 'This user does not exists or is not a borrower');
        }

        DB::beginTransaction();
        try{
            $this->repository->acceptLoanOffer($payload, $userID);
        }catch(ExpiredLoanOffer | InsufficientBalance |InvalidWallet $ex ){
            DB::rollBack();
            return $this->gene_response(false, $ex->getMessage());
        }
        catch(\Exception $e){
            report($e->getMessage());
            DB::rollBack();
            return $this->gene_response(false, 'Something went wrong accepting loan offer');
        }
        DB::commit();
        return $this->gene_response(true, 'Loan offer was accepted successfully');
    }

    //Borrower can decline a loan offer by any lender
    public function declineLoanOffer(array $payload, $userID)
    {
        if(!$this->verifyUser($userID,'borrower')){
            return $this->gene_response(false, 'This user does not exists or is not a borrower');
        }

        DB::beginTransaction();
        try{
            $this->repository->declineLoanOffer($payload, $userID);
        }catch(ExpiredLoanOffer  $ex ){
            DB::rollBack();
            return $this->gene_response(false, $ex->getMessage());
        }
        catch(\Exception $e){
            DB::rollBack();
            report($e->getMessage());
            return $this->gene_response(false, 'Something went wrong declining offer. Try again');
        }
        DB::commit();
        return $this->gene_response(true, 'Loan offer was declined successfully');
    }

    //Lender can see open loads to send an offer
    public function getOpenLoans()
    {
        $loans =  Loans::where('granted',false)->where('lender_id', null)->simplePaginate(20);
        if($loans){
            $links = $this->sortLinks($loans);
            return $this->gene_response(true, 'Open Loans', LoanCollection::make($loans),$links );
        }
        return $this->gene_response(false, 'Something went wring fetching loans');
    }

    private function verifyUser($id, $role) : bool
    {
       $user = User::find($id);
       if($user && $user->role->name == $role){
           return true;
       }
       return false;
    }
}
