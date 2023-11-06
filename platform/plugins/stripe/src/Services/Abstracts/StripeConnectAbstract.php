<?php

namespace Botble\Stripe\Services\Abstracts;

use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Botble\Stripe\Supports\StripeHelper;
use Exception;
use Stripe\Charge;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;
use Stripe\Refund;
use Stripe\Stripe;

abstract class StripeConnectAbstract
{
    public static function setClient(): bool
    {
        $secret = setting('payment_stripe_secret');
        $clientId = setting('payment_stripe_client_id');

        if (! $secret || ! $clientId) {
            return false;
        }

        Stripe::setApiKey($secret);
        Stripe::setClientId($clientId);

        return true;
    }
}
