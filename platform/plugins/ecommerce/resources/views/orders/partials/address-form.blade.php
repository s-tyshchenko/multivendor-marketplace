<div class="customer-address-payment-form">
    @if (EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check())
        <div class="mb-3 form-group">
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        </div>
    @endif

    {!! apply_filters('ecommerce_checkout_address_form_before') !!}

    @auth('customer')
        <div class="mb-3 form-group">
            @if ($isAvailableAddress)
                <label
                    class="mb-2 control-label"
                    for="address_id"
                >{{ __('Select available addresses') }}:</label>
            @endif
            @php
                $oldSessionAddressId = old('address.address_id', $sessionAddressId);
            @endphp
            <div class="list-customer-address @if (!$isAvailableAddress) d-none @endif">
                <div class="select--arrow">
                    <select
                        class="form-control"
                        id="address_id"
                        name="address[address_id]"
                    >
                        <option
                            value="new"
                            @selected($oldSessionAddressId == 'new')
                        >{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @selected($oldSessionAddressId == $address->id)
                                >{{ $address->full_address }}</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="fas fa-angle-down"></i>
                </div>
                <br>
                <div class="address-item-selected @if (!$sessionAddressId) d-none @endif">
                    @if ($isAvailableAddress && $oldSessionAddressId != 'new')
                        @if ($oldSessionAddressId && $addresses->contains('id', $oldSessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $addresses->firstWhere('id', $oldSessionAddressId),
                            ])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $defaultAddress,
                            ])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => Arr::first($addresses),
                            ])
                        @endif
                    @endif
                </div>
                <div class="list-available-address d-none">
                    @if ($isAvailableAddress)
                        @foreach ($addresses as $address)
                            <div
                                class="address-item-wrapper"
                                data-id="{{ $address->id }}"
                            >
                                @include(
                                    'plugins/ecommerce::orders.partials.address-item',
                                    compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endauth

    <div class="address-form-wrapper @if (auth('customer')->check() && $oldSessionAddressId !== 'new' && $isAvailableAddress) d-none @endif">
        <div class="form-group mb-3 @error('address.name') has-error @enderror">
            <div class="form-input-wrapper">
                <input
                    class="form-control"
                    id="address_name"
                    name="address[name]"
                    type="text"
                    value="{{ old('address.name', Arr::get($sessionCheckoutData, 'name')) ?: (auth('customer')->check() ? auth('customer')->user()->name : null) }}"
                    required
                >
                <label for="address_name">{{ __('Full Name') }}</label>
            </div>
            {!! Form::error('address.name', $errors) !!}
        </div>

        <div class="row">
            @if (!in_array('email', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class([
                    'col-12',
                ])>
                    <div class="form-group mb-3 @error('address.email') has-error @enderror">
                        <div class="form-input-wrapper">
                            <input
                                class="form-control"
                                id="address_email"
                                name="address[email]"
                                type="email"
                                value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) ?: (auth('customer')->check() ? auth('customer')->user()->email : null) }}"
                                required
                            >
                            <label for="address_email">{{ __('Email') }}</label>
                        </div>
                        {!! Form::error('address.email', $errors) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (!auth('customer')->check())
        <div class="mb-3 form-group @if(!$canCheckoutForSubscriptionProducts) d-none @endif">
            <input
                id="create_account"
                name="create_account"
                type="checkbox"
                value="1"
                @if (old('create_account') == 1 || !$canCheckoutForSubscriptionProducts) checked @endif
            >
            <label
                class="control-label"
                for="create_account"
            >{{ __('Register an account with above information?') }}</label>
        </div>
        @if (!$canCheckoutForSubscriptionProducts)
            <p>Your cart contains subscription products, you must create an account.</p>
        @endif
        <div class="password-group @if ($canCheckoutForSubscriptionProducts && !$errors->has('password') && !$errors->has('password_confirmation')) d-none @endif">
            <div class="row">
                <div class="col-12">
                    <div class="form-group  @error('password') has-error @enderror">
                        <div class="form-input-wrapper">
                            <input
                                class="form-control"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="password"
                            >
                            <label for="password">{{ __('Password') }}</label>
                        </div>
                        {!! Form::error('password', $errors) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {!! apply_filters('ecommerce_checkout_address_form_after', null, $sessionCheckoutData) !!}
</div>
