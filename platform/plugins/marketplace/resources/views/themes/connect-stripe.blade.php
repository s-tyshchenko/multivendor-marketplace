@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')

    {!! Form::open([
        'route' => 'marketplace.vendor.connect-stripe',
        'class' => 'ps-form--account-setting',
        'method' => 'POST',
    ]) !!}
    <div class="card-header">
        <h3 class="mb-0">{{ __('Connect Stripe') }}</h3>
    </div>
    <div class="ps-form__content">
        <div class="form-group">
            <label
                class="required"
                for="shop-name"
            >{{ __('Country') }}</label>
            <select
                class="form-control"
                id="country"
                name="country"
                type="text"
            >
                @foreach($countries as $key => $country)
                    <option value="{{ $key }}">{{ $country }}</option>
                @endforeach
            </select>

            @if ($errors->has('country'))
                <span class="text-danger">{{ $errors->first('country') }}</span>
            @endif
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" name="terms_and_conditions" type="checkbox" value="1">
            <label class="form-check-label" for="flexCheckDefault">
                {{ __('I agree to terms and conditions.') }}
            </label>
        </div>
        <div class="form-group text-center">
            <div class="form-group submit">
                <button class="submit submit-auto-width">{{ __('Submit') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}


@endsection
