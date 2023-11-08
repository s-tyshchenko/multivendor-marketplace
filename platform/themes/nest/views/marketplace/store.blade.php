@php
    Theme::asset()->container('footer')->usePath()->add('jquery.theia.sticky-js', 'js/plugins/jquery.theia.sticky.js');
    Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js', );
    Theme::asset()->container('footer')->add('custom-order.js', '/vendor/core/plugins/marketplace/js/custom-order.js');
@endphp

<form action="{{ $store->url }}" method="GET" id="products-filter-ajax" data-scroll-to=".products-listing">
    @if (theme_option('vendor_page_detail_layout') == 'list')
        @include(Theme::getThemeNamespace('views.marketplace.stores.list'))
    @else
        @include(Theme::getThemeNamespace('views.marketplace.stores.grid'))
    @endif
</form>

<div id="create-custom-order-modal" class="modal fade custom-modal" tabindex="-1"
     aria-labelledby="create-custom-order-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4>{{ __('plugins/marketplace::store.create_custom_order') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                {!! Form::open(['route' => 'public.cart.add-to-cart-custom', 'method' => 'POST']) !!}
                {!! Form::hidden('vendor_id', $store->customer_id) !!}


                <div class="form-group">
                    <label for="price">{{ __('Amount') }}:</label>
                    <input id="price"
                           type="number"
                           step="0.01"
                           class="form-control"
                           name="price"
                           placeholder="100.00">
                    {!! Form::error('price', $errors) !!}
                </div>

                <div class="form-group">
                    <label for="note">{{ __('Note') }}:</label>
                    <textarea id="note" class="form-control" rows="8" name="note" style="min-height: 150px;"></textarea>
                    {!! Form::error('note', $errors) !!}
                </div>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
