<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loans extends Model
{

    protected $fillable = [
        'amount',
        'reason',
        'borrower_id',
        'lender_id',
        'granted',
        'paid_back',
    ];

    use HasFactory;


    public function offers(): HasMany
    {
        return $this->hasMany(LoanOffers::class, 'loan_id');
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }
}
