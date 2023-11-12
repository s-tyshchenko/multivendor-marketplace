@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Your Subscriptions') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Vendor') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Next payment on') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse ($subscriptionsData as $subscription)
                            <tr>
                                <td>{{ $subscription['vendor_name'] }}</td>
                                <td>{{ $subscription['product_name'] }}</td>
                                <td>{{ $subscription['next_payment'] }}</td>
                                <td>{{ __(':price per :period', ['price' => $subscription['price'], 'period' => $subscription['period']]) }}</td>
                                <td>{{ $subscription['status'] }}</td>
                                <td>
                                    <a class="btn-small d-block" href="{{ route('customer.subscriptions.view', ['id' => $subscription['id'], 'vendor_id' => $subscription['vendor_id']]) }}">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">{{ __('No orders found!') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
