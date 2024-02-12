<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'research_id' => 'nullable',
            'process_id' => 'required',
            'office_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'files.*' => 'nullable|mimes:jpg,png,jpeg,pdf|max:20000'
        ];
    }

    public function messages()
    {
        return [
            'process_id.required' => 'The type field is required.',
            'office_id.required' => 'The section field is required.'
        ];
    }
}
