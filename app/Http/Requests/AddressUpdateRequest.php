<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['nullable', 'max:164'],
            'city' => ['nullable', 'max:128'],
            'province' => ['nullable', 'max:96'],
            'country' => ['required', 'max:96'],
            'postal_code' => ['nullable', 'max:12'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        return throw new HttpResponseException(response([
            'errors' => $validator->getMessageBag()
        ], 400));
    }
}
