<div class="row cart-item">
    <div class="col-3">
        <div class="checkout-product-img-wrapper">
            <img
                class="item-thumb img-thumbnail img-rounded"
                src="{{ RvMedia::getImageUrl(Arr::get($cartItem->options, 'image'), default: RvMedia::getDefaultImage()) }}"
                alt="{{ $product->original_product->name }}"
            >
            <span class="checkout-quantity">{{ $cartItem->qty }}</span>
        </div>
    </div>
    <div class="col">

        @if (!$cartItem->isCustom)
            <p class="mb-0">
                {{ $product->original_product->name }}
                @if ($product->isOutOfStock())
                    <span class="stock-status-label">({!! $product->stock_status_html !!})</span>
                @endif
            </p>
            <p class="mb-0">
                <small>{{ $product->variation_attributes }}</small>
            </p>
        @endif

            @if ($cartItem->options['note'])
                <p class="mb-0">Note</p>
                <p class="mb-0">
                    <small>{{ $cartItem->options['note'] }}</small>
                </p>
            @endif

        @include('plugins/ecommerce::themes.includes.cart-item-options-extras', [
            'options' => $cartItem->options,
        ])

        @if (!empty($cartItem->options['options']))
            {!! render_product_options_html($cartItem->options['options'], $product->original_price) !!}
        @endif
    </div>
    <div class="col-auto text-end">
        <p>{{ format_price($cartItem->price) }}</p>
        @if (!is_null($product->price_recurring_interval))
            <span>{{ __('per :interval', ['interval' => $product->price_recurring_interval]) }}</span>
        @endif
    </div>
</div>
