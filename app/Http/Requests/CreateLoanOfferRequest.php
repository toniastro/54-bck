<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoanOfferRequest extends FormRequest
{

    use RequestValidationTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'loan_id' => 'required|exists:loans,id',
            'maturity_date' => 'required|date|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'interest_rate' => 'required|int'
        ];
    }
}
