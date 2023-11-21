<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SubscriptionCartRequest extends CartRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|min:1',
            'qty' => 'integer|min:1',
            'is_custom' => 'nullable|boolean',
            'note' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __('Product ID is required'),
        ];
    }
}
