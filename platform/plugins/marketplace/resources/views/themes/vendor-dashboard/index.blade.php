@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
    @if (empty($store->name))
        @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.become-vendor-form'))
    @elseif (is_null($user->vendor_verified_at) || is_null($vendorInfo->stripe_connect_id))
        @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.stripe-banner'))
    @else
        @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.stripe-banner'))

        <div class="ps-card__content">
            {!! Form::open([
                'route' => 'marketplace.vendor.settings',
                'class' => 'ps-form--account-setting',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
            ]) !!}
            <div class="ps-form__content">
                <ul class="nav nav-tabs ">
                    <li class="nav-item">
                        <a
                            class="nav-link active"
                            data-bs-toggle="tab"
                            href="#tab_information"
                        >{{ __('General Information') }}</a>
                    </li>
                    {{--                @include('plugins/marketplace::customers.tax-info-tab')--}}
                    {{--                @include('plugins/marketplace::customers.payout-info-tab')--}}
                    {!! apply_filters('marketplace_vendor_settings_register_content_tabs', null, $store) !!}
                </ul>
                <div class="tab-content">
                    <div
                        class="tab-pane active"
                        id="tab_information"
                    >
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="required"
                                        for="shop-name"
                                    >{{ __('Shop Name') }}</label>
                                    <input
                                        class="form-control"
                                        id="shop-name"
                                        name="name"
                                        type="text"
                                        value="{{ old('name', $store->name) }}"
                                        placeholder="{{ __('Shop Name') }}"
                                    >
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <input
                                    name="reference_id"
                                    type="hidden"
                                    value="{{ $store->id }}"
                                >
                                <div class="form-group shop-url-wrapper">
                                    <label
                                        class="required float-start"
                                        for="shop-url"
                                    >{{ __('Shop URL') }}</label>
                                    <span class="d-inline-block float-end shop-url-status"></span>
                                    <input
                                        class="form-control"
                                        id="shop-url"
                                        name="slug"
                                        data-url="{{ route('public.ajax.check-store-url') }}"
                                        type="text"
                                        value="{{ old('slug', $store->slug) }}"
                                        placeholder="{{ __('Shop URL') }}"
                                    >
                                    @if ($errors->has('slug'))
                                        <span class="text-danger">{{ $errors->first('slug') }}</span>
                                    @endif
                                    <span class="d-inline-block"><small
                                            data-base-url="{{ route('public.store', old('slug', '')) }}"
                                        >{{ route('public.store', old('slug', $store->slug)) }}</small></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        class="required"
                                        for="shop-email"
                                    >{{ __('Shop Email') }}</label>
                                    <input
                                        class="form-control"
                                        id="shop-email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email', $store->email ?: $store->customer->email) }}"
                                        placeholder="{{ __('Shop Email') }}"
                                    >
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="logo">{{ __('Logo') }}</label>
                                    {!! Form::customImage('logo', old('logo', $store->logo)) !!}
                                    {!! Form::error('logo', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="logo">{{ __('Cover Image') }}</label>
                                    {!! Form::customImage('cover_image', old('cover_image', $store->getMetaData('cover_image', true))) !!}
                                    {!! Form::error('cover_image', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="3"
                            >{{ old('description', $store->description) }}</textarea>
                            {!! Form::error('description', $errors) !!}
                        </div>
                    </div>
                    {{--                @include('plugins/marketplace::customers.tax-form', ['model' => $store->customer])--}}
                    {{--                @include('plugins/marketplace::customers.payout-form', ['model' => $store->customer])--}}
                    {!! apply_filters('marketplace_vendor_settings_register_content_tab_inside', null, $store) !!}
                </div>

                <div class="form-group text-center">
                    <div class="form-group submit">
                        <div class="ps-form__submit text-center">
                            <button class="ps-btn success">{{ __('Save settings') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    @endif
@stop

@push('footer')
    <script>
        'use strict';

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = BotbleVariables.languages || {};
        BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
    </script>
@endpush
