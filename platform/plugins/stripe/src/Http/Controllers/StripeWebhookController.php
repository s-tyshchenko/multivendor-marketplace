<?php

namespace Botble\Stripe\Http\Controllers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\EmailHandler;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Models\VendorInfo;
use Botble\Support\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
    public function store(Request $request)
    {
        try {
            Stripe::setApiKey(setting('payment_stripe_secret'));
            Stripe::setClientId(setting('payment_stripe_client_id'));

            $event = \Stripe\Webhook::constructEvent(
                $request->input(),
                $request->header('HTTP_STRIPE_SIGNATURE'),
                setting('payment_stripe_webhook_secret')
            );
        } catch(\UnexpectedValueException | \Stripe\Exception\SignatureVerificationException $e) {
            http_response_code(400);
            exit();
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
        } else if ($event->type == 'account.updated') {
            $account = $event->data->object;
            if ($account->details_submitted) {
                $vendorInfo = VendorInfo::query()->where(['stripe_connect_id' => $account->id])->firstOrFail();
                $vendor = Customer::query()
                    ->where([
                        'id' => $vendorInfo->customer_id,
                        'is_vendor' => true,
                        'vendor_verified_at' => null,
                    ])
                    ->firstOrFail();
                $vendor->vendor_verified_at = Carbon::now();
                $vendor->save();

                event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $vendor));

                if (MarketplaceHelper::getSetting('verify_vendor', 1) && ($vendor->store->email || $vendor->email)) {
                    EmailHandler::setModule(MARKETPLACE_MODULE_SCREEN_NAME)
                        ->setVariableValues([
                            'store_name' => $vendor->store->name,
                        ])
                        ->sendUsingTemplate('vendor-account-approved', $vendor->store->email ?: $vendor->email);
                }
            }
        }
    }
}
