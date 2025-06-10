<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_address.delivery_date' => 'required|date',
            'shipping_address.date_first_game' => 'required|date',
            'shipping_address.organization_name' => 'required|string',
            'shipping_address.first_name' => 'required|string',
            'shipping_address.last_name' => 'required|string|min:2',
            'shipping_address.email' => 'required|email|confirmed',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address1' => 'required|string',
            'shipping_address.address2' => 'nullable',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.zip' => 'required|string',
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sku' => 'required|string',
        ];
    }
}
