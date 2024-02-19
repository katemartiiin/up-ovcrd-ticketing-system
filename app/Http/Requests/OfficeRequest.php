<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfficeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'short_code' => 'required',
            'head_id' => 'nullable',
            'description' => 'nullable',
            'users' => 'nullable',
            'processes' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
