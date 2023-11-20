@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    <h2 class="customer-page-title">{{ __('Order information') }}</h2>
    <div class="clearfix"></div>
    <br>

    <div class="customer-order-detail">

        <div class="row">
            <div class="col-md-6">
                <div class="order-slogan">
                    @if ($logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo'))
                        <img
                            src="{{ RvMedia::getImageUrl($logo) }}"
                            alt="{{ theme_option('site_title') }}"
                            width="100"
                        >
                        <br />
                    @endif
                    {{ setting('contact_address') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="order-meta">
                    <p>
                        <span>{{ __('Order number') }}:</span>
                        <span class="order-detail-value">{{ $order->code }}</span>
                    </p>
                    <span>{{ __('Time') }}:</span>
                    <span class="order-detail-value">{{ $order->created_at->format('h:m d/m/Y') }}</span>
                </div>
            </div>
        </div>

    @endsection
