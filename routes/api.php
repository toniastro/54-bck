<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[RegisterController::class,'register'])->name('user.register');

Route::put('top-wallet/{userID}',[WalletController::class,'update'])->whereNumber('userID')->name('user.lender.top_wallet');
Route::get('loans',[LoanController::class,'getLoans'])->name('user.lender.open_loans');
Route::post('loans/borrow/{userID}',[LoanController::class,'createLoan'])->whereNumber('userID')->name('user.borrower.create.loan');
Route::prefix('loan-offers/{userID}')->group(function () {
    Route::post('/', [LoanController::class, 'createLoanOffer'])->whereNumber('userID')->name('user.lender.create.offer');
    Route::put('/accept', [LoanController::class, 'acceptLoanOffer'])->whereNumber('userID')->name('user.borrower.decline.offer');
    Route::put('/decline', [LoanController::class, 'declineLoanOffer'])->whereNumber('userID')->name('user.borrower.decline.offer');
});


