<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleFilterRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_from' => 'nullable|date',
            'end_to' => 'required_with:start_from|date',
            'approval_status' => 'nullable|in:0,1,2',
        ];
    }
}
