<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanOfferRequest extends FormRequest
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
            'loan_offer_id' => 'required|exists:loan_offers,id',
            'message' => 'string|nullable|max:200'
        ];
    }
}
