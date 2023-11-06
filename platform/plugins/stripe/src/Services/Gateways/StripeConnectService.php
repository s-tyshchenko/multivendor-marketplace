<?php

namespace Botble\Stripe\Services\Gateways;

use Botble\Stripe\Services\Abstracts\StripeConnectAbstract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class StripeConnectService extends StripeConnectAbstract
{
    public static function createAccount(string $email)
    {
        if (!self::setClient()) {
            return null;
        }

        try {
            return \Stripe\Account::create([
                'type' => 'express',
                'email' => $email
            ]);
        } catch (\Exception $exception) {
            self::handleError($exception);
            return null;
        }
    }

    public static function getAccount(string $id)
    {
        if (!self::setClient()) {
            return null;
        }

        try {
            return \Stripe\Account::retrieve($id);
        } catch (\Exception $exception) {
            self::handleError($exception);
            return null;
        }
    }

    public static function getOnboardingLink(string $id)
    {
        if (!self::setClient()) {
            return null;
        }

        try {
            $accountLinks = \Stripe\AccountLink::create([
                'account' => $id,
                'refresh_url' => config('app.url') . '/vendor/dashboard',
                'return_url' => config('app.url') . '/vendor/dashboard',
                'type' => 'account_onboarding',
            ]);

            return $accountLinks->url;
        } catch (\Exception $exception) {
            self::handleError($exception);
            return null;
        }
    }

    public static function getLoginLink(string $id)
    {
        return "https://dashboard.stripe.com/accounts/{$id}";
    }

    protected static function handleError(\Exception $exception)
    {
        Log::error(
            'Failed to make a payment charge.',
            [
                'user_id' => Auth::check() ? Auth::id() : 0,
                'ip' => Request::ip(),
                'line' => __LINE__,
                'function' => __FUNCTION__,
                'class' => __CLASS__,
                'userAgent' => Request::header('User-Agent'),
                'error_message' => $exception->getMessage(),
            ]
        );

    }

}
