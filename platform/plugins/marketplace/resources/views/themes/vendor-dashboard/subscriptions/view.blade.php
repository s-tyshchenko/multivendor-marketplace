@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="card">
    <div class="card-body">
        <div class="customer-order-detail">
            @include('plugins/ecommerce::themes.includes.subscription-tracking-detail')

            <div class="mt-4 row">
                <div class="col-auto">
                    <a href="{{ route('marketplace.vendor.subscriptions.cancel', ['id' => $subscription->id]) }}"
                       onclick="return confirm('{{ __('Are you sure?') }}')"
                       class="btn btn-danger btn-sm ml-2">{{ __('Cancel subscription') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper-content bg-gray-white mb20">
    <div class="pd-all-20">
        <h5>Send message to a customer</h5>
        {!! Form::open(['method' => 'POST', 'route' => ['marketplace.vendor.orders.send-message-to-customer', $order->id]]) !!}
        @csrf

        <div class="form-group">
            <label class="form-label">Message</label>
            <textarea class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send</button>
        {!! Form::close() !!}
    </div>
</div>
@endsection
