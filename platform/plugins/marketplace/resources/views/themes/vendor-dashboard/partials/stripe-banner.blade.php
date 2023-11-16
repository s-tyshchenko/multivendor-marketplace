<div
    class="alert alert-success"
    role="alert"
>
    @if (empty($user->vendorInfo->stripe_connect_id) || !$stripeConnectAccount->details_submitted)
        <h4 class="alert-heading">
            {{ __('Get started with Stripe Connect on our platform!') }}
        </h4>
        <p>{{ __('Connect Stripe Connect: Onboard to sell and receive payments') }}</p>
        <button
            id="vendor-retrieve-stripe-connect-link"
            class="btn btn-primary"
            data-action="{{ route('payments.stripe.connect-link') }}"
        >Create Stripe Connect Account
        </button>
    @else
        <h4 class="alert-heading">
            {{ __('Manage your Stripe Connect account') }}
        </h4>
        <p>{{ __('Access your ordsers and earnings with ease') }}</p>
        <button
            id="vendor-retrieve-stripe-connect-link"
            class="btn btn-primary"
            data-action="{{ route('payments.stripe.connect-link') }}"
        >Log in to Stripe
        </button>
    @endif
</div>
