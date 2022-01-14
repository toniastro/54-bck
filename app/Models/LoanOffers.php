<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanOffers extends Model
{

    public static $open = 'open';

    public static  $accepted = 'accepted';

    public static  $rejected = 'rejected';

    protected $fillable = [
        'loan_id',
        'lender_id',
        'interest_rate',
        'maturity_date',
        'status',
    ];

    use HasFactory;

    public function loans(): BelongsTo
    {
        return $this->belongsTo(Loans::class, 'loan_id');
    }

}
