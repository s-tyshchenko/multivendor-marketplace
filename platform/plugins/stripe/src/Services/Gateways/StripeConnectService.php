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

    public static function getAllowedCountriesList()
    {
        return array(
            "AG" => "Antigua & Barbuda",
            "CZ" => "Czech Republic",
            "IS" => "Iceland",
            "MD" => "Moldova",
            "SM" => "San Marino",
            "GB" => "United Kingdom",
            "AM" => "Armenia",
            "CI" => "Côte d’Ivoire",
            "IN" => "India",
            "MC" => "Monaco",
            "SA" => "Saudi Arabia",
            "US" => "United States",
            "AR" => "Argentina",
            "DK" => "Denmark",
            "ID" => "Indonesia",
            "MA" => "Morocco",
            "SN" => "Senegal",
            "UY" => "Uruguay",
            "AU" => "Australia",
            "DO" => "Dominican Republic",
            "IE" => "Ireland",
            "NA" => "Namibia",
            "RS" => "Serbia",
            "VN" => "Vietnam",
            "AT" => "Austria",
            "EC" => "Ecuador",
            "IL" => "Israel",
            "NL" => "Netherlands",
            "SG" => "Singapore",
            "BH" => "Bahrain",
            "EG" => "Egypt",
            "IT" => "Italy",
            "NZ" => "New Zealand",
            "SK" => "Slovakia",
            "BD" => "Bangladesh",
            "SV" => "El Salvador",
            "JM" => "Jamaica",
            "NG" => "Nigeria",
            "SI" => "Slovenia",
            "BE" => "Belgium",
            "EE" => "Estonia",
            "JP" => "Japan",
            "MK" => "North Macedonia",
            "ZA" => "South Africa",
            "BJ" => "Benin",
            "FI" => "Finland",
            "KE" => "Kenya",
            "NO" => "Norway",
            "KR" => "South Korea",
            "BO" => "Bolivia",
            "FR" => "France",
            "KW" => "Kuwait",
            "OM" => "Oman",
            "ES" => "Spain",
            "BA" => "Bosnia & Herzegovina",
            "GM" => "Gambia",
            "LV" => "Latvia",
            "PA" => "Panama",
            "LC" => "St Lucia",
            "BG" => "Bulgaria",
            "DE" => "Germany",
            "LI" => "Liechtenstein",
            "PY" => "Paraguay",
            "SE" => "Sweden",
            "CA" => "Canada",
            "GH" => "Ghana",
            "LT" => "Lithuania",
            "PE" => "Peru",
            "CH" => "Switzerland",
            "CL" => "Chile",
            "GR" => "Greece",
            "LU" => "Luxembourg",
            "PH" => "Philippines",
            "TH" => "Thailand",
            "CO" => "Colombia",
            "GT" => "Guatemala",
            "MY" => "Malaysia",
            "PL" => "Poland",
            "TT" => "Trinidad & Tobago",
            "CR" => "Costa Rica",
            "GY" => "Guyana",
            "MT" => "Malta",
            "PT" => "Portugal",
            "TN" => "Tunisia",
            "HR" => "Croatia",
            "HK" => "Hong Kong SAR China",
            "MU" => "Mauritius",
            "QA" => "Qatar",
            "TR" => "Turkey",
            "CY" => "Cyprus",
            "HU" => "Hungary",
            "MX" => "Mexico",
            "RO" => "Romania",
            "AE" => "United Arab Emirates"
        );

    }
}
