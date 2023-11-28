@if ($subscription)
    <div class="customer-order-detail">
        <div class="row">
            <div class="col-md-6">
                <p>
                    <span class="d-inline-block me-1">{{ __('Order number') }}: </span>
                    <strong>{{ $subscription->id }}</strong>
                </p>
                <p>
                    <span class="d-inline-block me-1">{{ __('Created at') }}: </span>
                    <strong>{{ date('Y-m-d H:i:s', $subscription->created) }}</strong>
                </p>
                <p>
                    <span class="d-inline-block me-1">{{ __('Next payment at') }}: </span>
                    <strong>{{ date('Y-m-d H:i:s', $subscription->current_period_end) }}</strong>
                </p>
                <p>
                    <span class="d-inline-block me-1">{{ __('Status') }}: </span>
                    <strong class="text-info">{{ $subscription->status }}</strong>
                </p>
                @if (isset($store) && !empty($store))
                <p>
                    <span class="d-inline-block me-1">{{ __('Store') }}: </span>
                    <strong class="text-info">{{ $store->name }}</strong>
                </p>
                @endif
                @if (isset($customer) && !empty($customer))
                <p>
                    <span class="d-inline-block me-1">{{ __('Customer') }}: </span>
                    <strong class="text-info">{{ $customer->name }}</strong>
                </p>
                @endif
                @if (isset($orderProduct) && !empty($orderProduct))
                    <p>
                        <span class="d-inline-block me-1">{{ __('Note') }}: </span>
                        <strong class="text-info">{{ $orderProduct->options['note'] ?? '' }}</strong>
                    </p>
                @endif
                <p>
                    <span class="d-inline-block me-1">{{ __('Product') }}: </span>
                    <strong class="text-info">{{ $product->name }}</strong>
                </p>
            </div>
        </div>

        <br>
        <h5 class="mb-3">{{ __('Invoices') }}</h5>
        <div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                    <tr>
                        <th class="">{{ __('Date') }}</th>
                        <th class="price">{{ __('Amount') }}</th>
                        <th class="">{{ __('Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ date('Y-m-d H:i:s', $invoice->created) }}</td>
                            <td>{{ strtoupper($invoice->currency) }} {{ $invoice->amount_paid / 100 }}</td>
                            <td>{{ $invoice->status }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@elseif (request()->input('order_id') || request()->input('email'))
    <p class="text-center text-danger">{{ __('Order not found!') }}</p>
@endif
