@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Hello :name!', ['name' => auth('customer')->user()->name]) }} </h5>
        </div>
    </div>
@endsection
