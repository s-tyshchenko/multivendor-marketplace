@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="customer-order-detail">
            @include('plugins/ecommerce::themes.includes.subscription-tracking-detail')

            <div class="mt-4 row">

                <div class="col-auto">
                    <a href="{{ route('customer.subscriptions.cancel', ['id' => $subscription->id, 'vendor_id' => $store->customer_id]) }}"
                       onclick="return confirm('{{ __('Are you sure?') }}')"
                       class="btn btn-danger btn-sm ml-2">{{ __('Cancel subscription') }}</a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
