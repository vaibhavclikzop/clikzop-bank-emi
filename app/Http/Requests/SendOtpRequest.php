<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'mobile' => [
                'required',
                'string',
                'regex:/^[6-9][0-9]{9}$/',
            ],

            'purpose' => [
                'required',
                'string',
                'in:login,register,forgot_password,verify_mobile',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Please enter a valid Indian mobile number.',
            'purpose.required' => 'OTP purpose is required.',
            'purpose.in' => 'Invalid OTP purpose.',
        ];
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
