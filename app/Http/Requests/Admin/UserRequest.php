<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'office_id' => 'required',
            'role' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'office_id.required' => 'The section field is required.'
        ];
    }
}
