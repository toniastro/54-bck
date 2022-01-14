<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ResponseTrait;

trait RequestValidationTrait {

    use ResponseTrait;

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException($this->gene_response(false, 'Error in data sent', $validator->errors()));
    }
}
