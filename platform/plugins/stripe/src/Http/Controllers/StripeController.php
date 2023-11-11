<?php

namespace Botble\Stripe\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Models\VendorInfo;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Stripe\Http\Requests\StripePaymentCallbackRequest;
use Botble\Stripe\Services\Gateways\StripeConnectService;
use Botble\Stripe\Services\Gateways\StripePaymentService;
use Botble\Support\Http\Requests\Request;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeController extends Controller
{
    public function connect()
    {
        Stripe::setApiKey(setting('payment_stripe_secret'));
        Stripe::setClientId(setting('payment_stripe_client_id'));

        /** @var Customer $customer */
        $customer = auth('customer')->user();

        /** @var VendorInfo $vendorInfo */
        $vendorInfo = $customer->vendorInfo;

        if (!$customer->is_vendor || empty($vendorInfo)) {
            return response()->setStatusCode(403, 'You\'re not allowed to access this action');
        }

        if (empty($vendorInfo->stripe_connect_id)) {
            $stripeAccount = StripeConnectService::createAccount($customer->email);

            $vendorInfo->setAttribute('stripe_connect_id', $stripeAccount->id);
            $vendorInfo->save();
        }

        if (!isset($stripeAccount)) {
            $stripeAccount = StripeConnectService::getAccount($vendorInfo->stripe_connect_id);
        }

        if (!$stripeAccount->details_submitted) {
            $link = StripeConnectService::getOnboardingLink($vendorInfo->stripe_connect_id);
        } else {
            $link = StripeConnectService::getLoginLink($vendorInfo->stripe_connect_id);
        }

        return response()->json(['link' => $link]);
    }

    public function success(
        StripePaymentCallbackRequest $request,
        StripePaymentService $stripePaymentService,
        BaseHttpResponse $response
    )
    {
        try {
            $stripePaymentService->setClient();

            $options = [];
            if (get_payment_setting('payment_type', STRIPE_PAYMENT_METHOD_NAME) == 'stripe_connect') {
                $options['stripe_account'] = $request->input('account_id');
            }

            $session = Session::retrieve($request->input('session_id'), $options);

            if ($session->payment_status == 'paid') {
                $metadata = $session->metadata->toArray();

                $orderIds = json_decode($metadata['order_id'], true);

                if ($session->mode == "payment") {
                    $charge = PaymentIntent::retrieve($session->payment_intent, $options);

                    if (!$charge->latest_charge) {
                        return $response
                            ->setError()
                            ->setNextUrl(PaymentHelper::getCancelURL())
                            ->setMessage(__('No payment charge. Please try again!'));
                    }

                    $chargeId = $charge->latest_charge;

                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'amount' => $metadata['amount'],
                        'currency' => strtoupper($session->currency),
                        'charge_id' => $chargeId,
                        'order_id' => $orderIds,
                        'customer_id' => Arr::get($metadata, 'customer_id'),
                        'customer_type' => Arr::get($metadata, 'customer_type'),
                        'payment_channel' => STRIPE_PAYMENT_METHOD_NAME,
                        'status' => PaymentStatusEnum::COMPLETED,
                    ]);
                } else {
                    $subscription = Subscription::retrieve($session->subscription, $options);

                    if (!$subscription->latest_invoice) {
                        return $response
                            ->setError()
                            ->setNextUrl(PaymentHelper::getCancelURL())
                            ->setMessage(__('No payment invoice. Please try again!'));
                    }

                    $invoiceId = $subscription->latest_invoice;

                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'amount' => $metadata['amount'],
                        'currency' => strtoupper($session->currency),
                        'charge_id' => $invoiceId,
                        'order_id' => $orderIds,
                        'customer_id' => Arr::get($metadata, 'customer_id'),
                        'customer_type' => Arr::get($metadata, 'customer_type'),
                        'payment_channel' => STRIPE_PAYMENT_METHOD_NAME,
                        'status' => PaymentStatusEnum::COMPLETED,
                    ]);
                }
                return $response
                    ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $chargeId)
                    ->setMessage(__('Checkout successfully!'));
            }

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage($exception->getMessage() ?: __('Payment failed!'));
        }
    }

    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage(__('Payment failed!'));
    }
}
