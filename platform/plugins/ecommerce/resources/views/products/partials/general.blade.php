{!! apply_filters('ecommerce_product_variation_form_start', null, $product) !!}
<div class="row price-group">
    <input
        class="detect-schedule hidden"
        name="sale_type"
        type="hidden"
        value="{{ old('sale_type', $product ? $product->sale_type : 0) }}"
    >
    <input
        name="product_id"
        type="hidden"
        value="{{ $product->id ?? null }}"
    >

    <div class="col-md-4 d-none">
        <div class="form-group mb-3 @if ($errors->has('sku')) has-error @endif">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.sku') }}</label>
            {!! Form::text('sku', old('sku', $product ? $product->sku : (new \Botble\Ecommerce\Models\Product())->generateSku()), ['class' => 'next-input', 'id' => 'sku']) !!}
        </div>
        @if (($isVariation && !$product) || ($product && $product->is_variation && !$product->sku))
            <div class="form-group mb-3">
                <label class="text-title-field">
                    <input
                        name="auto_generate_sku"
                        type="hidden"
                        value="0"
                    >
                    <input
                        name="auto_generate_sku"
                        type="checkbox"
                        value="1"
                    >
                    &nbsp;{{ trans('plugins/ecommerce::products.form.auto_generate_sku') }}
                </label>
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.price') }}</label>
            <div class="next-input--stylized">
                <span
                    class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                <input
                    class="next-input input-mask-number regular-price next-input--invisible"
                    name="price"
                    data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                    data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}"
                    type="text"
                    value="{{ old('price', $product ? $product->price : $originalProduct->price ?? 0) }}"
                    step="any"
                >
            </div>
        </div>
    </div>

    <div class="col-md-6 price-recurring-interval">
        <div class="form-group mb-3">
            <label class="text-title-field">{{ trans('plugins/ecommerce::products.form.price_recurring_interval') }}</label>
            <select
                class="next-input price-recurring-interval"
                name="price_recurring_interval"
            >
                <option {{ old('price_recurring_interval', ($product ? $product->price_recurring_interval : $originalProduct->price_recurring_interval ?? null) == null ? "selected=\"1\"" : "") }} value="">None</option>
                <option {{ old('price_recurring_interval', ($product ? $product->price_recurring_interval : $originalProduct->price_recurring_interval ?? null) == "day" ? "selected=\"1\"" : "") }} value="day">Day</option>
                <option {{ old('price_recurring_interval', ($product ? $product->price_recurring_interval : $originalProduct->price_recurring_interval ?? null) == "week" ? "selected=\"1\"" : "") }} value="week">Week</option>
                <option {{ old('price_recurring_interval', ($product ? $product->price_recurring_interval : $originalProduct->price_recurring_interval ?? null) == "month" ? "selected=\"1\"" : "") }} value="month">Month</option>
                <option {{ old('price_recurring_interval', ($product ? $product->price_recurring_interval : $originalProduct->price_recurring_interval ?? null) == "year" ? "selected=\"1\"" : "") }} value="year">Year</option>
            </select>
        </div>
    </div>
</div>

{!! apply_filters('ecommerce_product_variation_form_middle', null, $product) !!}

{!! apply_filters('ecommerce_product_variation_form_end', null, $product) !!}
