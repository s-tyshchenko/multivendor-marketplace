<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CustomCartRequest extends Request
{
    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:Botble\Ecommerce\Models\Customer,id',
            'price' => 'required|decimal:0,2',
            'note' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'id.price' => __('Product ID is required'),
        ];
    }
}
