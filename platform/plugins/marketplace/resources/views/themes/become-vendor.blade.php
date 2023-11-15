@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.become-vendor-form'))
@endsection
