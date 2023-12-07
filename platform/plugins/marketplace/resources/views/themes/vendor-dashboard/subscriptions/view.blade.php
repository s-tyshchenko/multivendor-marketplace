@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="card">
    <div class="card-body">
        <div class="customer-order-detail">
            @include('plugins/ecommerce::themes.includes.subscription-tracking-detail')
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
            <textarea class="form-control" name="message"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send</button>
        {!! Form::close() !!}
    </div>
</div>
@endsection
