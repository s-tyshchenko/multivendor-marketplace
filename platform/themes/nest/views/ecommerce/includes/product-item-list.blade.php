<div class="product-cart-wrap">
    <div class="product-img-action-wrap">
        <div class="product-img product-img-zoom">
            <div class="product-img-inner">
                <a href="{{ $product->url }}">
                    <img class="default-img" src="{{ RvMedia::getImageUrl($product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                    <img class="hover-img" src="{{ RvMedia::getImageUrl(Arr::get($product->images, 1, $product->image), 'product-thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                </a>
            </div>
        </div>
        <div class="product-action-1">
            <a aria-label="{{ __('Quick View') }}" href="#" class="action-btn hover-up js-quick-view-button" data-url="{{ route('public.ajax.quick-view', $product->id) }}">
                <i class="fi-rs-eye"></i>
            </a>
            @if (EcommerceHelper::isWishlistEnabled())
                <a aria-label="{{ __('Add To Wishlist') }}" href="#" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}">
                    <i class="fi-rs-heart"></i>
                </a>
            @endif
            @if (EcommerceHelper::isCompareEnabled())
                <a aria-label="{{ __('Add To Compare') }}" href="#" class="action-btn hover-up js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}">
                    <i class="fi-rs-shuffle"></i>
                </a>
            @endif
        </div>
        <div class="product-badges product-badges-position product-badges-mrg">
            @if ($product->isOutOfStock())
                <span class="bg-dark" style="font-size: 11px;">{{ __('Out Of Stock') }}</span>
            @else
                @if ($product->productLabels->count())
                    @foreach ($product->productLabels as $label)
                        <span @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                    @endforeach
                @else
                    @if ($product->front_sale_price !== $product->price)
                        <span class="hot">{{ get_sale_percentage($product->price, $product->front_sale_price) }}</span>
                    @endif
                @endif
            @endif
        </div>
    </div>
    <div class="product-content-wrap">
        @if ($category = $product->categories->sortByDesc('id')->first())
            <div class="product-category">
                <a href="{{ $category->url }}">{!! BaseHelper::clean($category->name) !!}</a>
            </div>
        @endif
        <h2 class="text-truncate"><a href="{{ $product->url }}" title="{{ $product->name }}">{{ $product->name }}</a></h2>
        @if (EcommerceHelper::isReviewEnabled() && $product->reviews_count)
            <div class="product-rate-cover">
                <div class="product-rate d-inline-block">
                    <div class="product-rating" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                </div>
                <span class="font-small ml-5 text-muted">({{ $product->reviews_count }})</span>
            </div>
        @endif
        @if (is_plugin_active('marketplace') && $product->store->id)
            <div>
                <span class="font-small text-muted">{{ __('Sold By') }} <a href="{{ $product->store->url }}">{!! BaseHelper::clean($product->store->name) !!}</a></span>
            </div>
        @endif
        <p class="mt-15 mb-15">{!! BaseHelper::clean($product->description) !!}</p>

        {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!}
        <div class="product-price">
            <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
            @if ($product->front_sale_price !== $product->price)
                <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
            @endif
        </div>
        {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}

        <div class="mt-30 d-flex align-items-center">
            @if (EcommerceHelper::isCartEnabled())
                <a aria-label="{{ __('Add To Cart') }}"
                    class="btn add-to-cart-button"
                    data-id="{{ $product->id }}"
                    data-url="{{ route('public.ajax.cart.store') }}"
                    href="#">
                    <i class="fi-rs-shopping-cart mr-5"></i>{{ __('Add to Cart') }}
                </a>
            @endif
            @if (EcommerceHelper::isCompareEnabled())
                <a aria-label="{{ __('Add To Compare') }}" href="#" class="add-wishlish ml-30 text-body font-sm font-heading font-weight-bold js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}">
                    <i class="fi-rs-shuffle mr-5"></i>{{ __('Add Compare') }}
                </a>
            @endif
        </div>
    </div>
</div>
