@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
<div class="card">
    <div class="card-body">
        <div class="customer-order-detail">
            @include('plugins/ecommerce::themes.includes.subscription-tracking-detail')

            <div class="mt-4 row">

                <div class="col-auto">
                    <a href="{{ route('subscriptions.cancel', ['id' => $subscription->id, 'vendor_id' => $store->customer_id]) }}"
                       onclick="return confirm('{{ __('Are you sure?') }}')"
                       class="btn btn-danger btn-sm ml-2">{{ __('Cancel subscription') }}</a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Send message to vendor</h5>
        {!! Form::open(['method' => 'POST', 'route' => ['customer.orders.send-email-to-vendor', $order->id]]) !!}
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
