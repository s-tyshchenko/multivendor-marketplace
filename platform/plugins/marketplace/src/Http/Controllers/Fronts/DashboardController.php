<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Http\Requests\BecomeVendorRequest;
use Botble\Marketplace\Models\Revenue;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Models\VendorInfo;
use Botble\Marketplace\Models\Withdrawal;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Stripe\Services\Gateways\StripeConnectService;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stripe\Stripe;

class DashboardController
{
    public function __construct()
    {
        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css');

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'])
            ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'])
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery']);
    }

    public function index(Request $request, BaseHttpResponse $response)
    {
        PageTitle::setTitle(__('Dashboard'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/ecommerce/libraries/daterangepicker/daterangepicker.js',
            'vendor/core/plugins/ecommerce/libraries/apexcharts-bundle/dist/apexcharts.min.js',
            'vendor/core/plugins/ecommerce/js/report.js',
            'vendor/core/plugins/marketplace/js/stripe-connect.js',
        ])
            ->addStylesDirectly([
                'vendor/core/plugins/ecommerce/libraries/daterangepicker/daterangepicker.css',
                'vendor/core/plugins/ecommerce/libraries/apexcharts-bundle/dist/apexcharts.css',
                'vendor/core/plugins/ecommerce/css/report.css',
            ])
            ->addScripts(['moment']);

        Assets::usingVueJS();

        [$startDate, $endDate, $predefinedRange] = EcommerceHelper::getDateRangeInReport($request);

        $user = auth('customer')->user();

        if (empty($user->vendorInfo->stripe_connect_id)) {
            $stripeConnectAccount = StripeConnectService::createAccount($user->email);
        } else {
            $stripeConnectAccount = StripeConnectService::getAccount($user->vendorInfo->stripe_connect_id);
        }

        if ($stripeConnectAccount->charges_enabled) {
            $this->approveVendor($user, $request);
        }

        $store = $user->store;
        $vendorInfo = $user->vendorInfo;
        $data = compact('startDate', 'endDate', 'predefinedRange');

        $revenue = Revenue::query()
            ->selectRaw(
                'SUM(CASE WHEN type IS NULL OR type = ? THEN sub_amount WHEN type = ? THEN sub_amount * -1 ELSE 0 END) as sub_amount,
                SUM(CASE WHEN type IS NULL OR type = ? THEN amount WHEN type = ? THEN amount * -1 ELSE 0 END) as amount,
                SUM(fee) as fee',
                [RevenueTypeEnum::ADD_AMOUNT, RevenueTypeEnum::SUBTRACT_AMOUNT, RevenueTypeEnum::ADD_AMOUNT, RevenueTypeEnum::SUBTRACT_AMOUNT]
            )
            ->where('customer_id', $user->getKey())
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->groupBy('customer_id')
            ->first();

        $withdrawal = Withdrawal::query()
            ->select([
                DB::raw('SUM(mp_customer_withdrawals.amount) as amount'),
                DB::raw('SUM(mp_customer_withdrawals.fee)'),
            ])
            ->where('mp_customer_withdrawals.customer_id', $user->getKey())
            ->whereIn('mp_customer_withdrawals.status', [
                WithdrawalStatusEnum::COMPLETED,
                WithdrawalStatusEnum::PENDING,
                WithdrawalStatusEnum::PROCESSING,
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('mp_customer_withdrawals.created_at', '>=', $startDate)
                    ->whereDate('mp_customer_withdrawals.created_at', '<=', $endDate);
            })
            ->groupBy('mp_customer_withdrawals.customer_id')
            ->first();

        $revenues = collect([
            'amount' => $revenue ? $revenue->amount : 0,
            'fee' => ($revenue ? $revenue->fee : 0) + ($withdrawal ? $withdrawal->fee : 0),
            'sub_amount' => $revenue ? $revenue->sub_amount : 0,
            'withdrawal' => $withdrawal ? $withdrawal->amount : 0,
        ]);

        $data['revenue'] = $revenues;

        $data['orders'] = Order::query()
            ->select([
                'id',
                'status',
                'user_id',
                'created_at',
                'amount',
                'tax_amount',
                'shipping_amount',
                'payment_id',
            ])
            ->with(['user', 'payment'])
            ->where([
                'is_finished' => 1,
                'store_id' => $store->id,
            ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $data['products'] = Product::query()
            ->select([
                'id',
                'name',
                'order',
                'created_at',
                'status',
                'sku',
                'images',
                'price',
                'sale_price',
                'sale_type',
                'start_date',
                'end_date',
                'quantity',
                'with_storehouse_management',
            ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where([
                'is_variation' => false,
                'store_id' => $store->id,
            ])
            ->wherePublished()
            ->limit(10)
            ->get();

        $totalProducts = $store->products()->count();
        $totalOrders = $store->orders()->count();
        $compact = compact('user', 'stripeConnectAccount', 'vendorInfo', 'store', 'data', 'totalProducts', 'totalOrders');

        if ($request->ajax()) {
            return $response
                ->setData([
                    'html' => MarketplaceHelper::view('vendor-dashboard.partials.dashboard-content', $compact)->render(),
                ]);
        }

        return MarketplaceHelper::view('vendor-dashboard.index', $compact);
    }

    public function postUpload(Request $request, BaseHttpResponse $response)
    {
        $customer = auth('customer')->user();

        $uploadFolder = $customer->store?->upload_folder ?: $customer->upload_folder;

        if (!RvMedia::isChunkUploadEnabled()) {
            $validator = Validator::make($request->all(), [
                'file.0' => 'required|image|mimes:jpg,jpeg,png',
            ]);

            if ($validator->fails()) {
                return $response->setError()->setMessage($validator->getMessageBag()->first());
            }

            $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, $uploadFolder);

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            return $response->setData($result['data']);
        }

        try {
            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = RvMedia::handleUpload($save->getFile(), 0, $uploadFolder);

                if (!$result['error']) {
                    return $response->setData($result['data']);
                }

                return $response->setError()->setMessage($result['message']);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    public function postUploadFromEditor(Request $request)
    {
        return RvMedia::uploadFromEditor($request);
    }

    public function getBecomeVendor()
    {
        $customer = auth('customer')->user();
        if ($customer->is_vendor) {
            return redirect()->route('marketplace.vendor.dashboard');
        }

        SeoHelper::setTitle(__('Become Vendor'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Become Vendor'), route('marketplace.vendor.become-vendor'));

        return Theme::scope('marketplace.become-vendor', [], MarketplaceHelper::viewPath('become-vendor', false))
            ->render();
    }

    public function postBecomeVendor(BecomeVendorRequest $request, BaseHttpResponse $response)
    {
        $customer = auth('customer')->user();

        $existing = SlugHelper::getSlug($request->input('shop_url'), SlugHelper::getPrefix(Store::class));

        if ($existing) {
            return $response->setError()->setMessage(__('Shop URL is existing. Please choose another one!'));
        }

        event(new Registered($customer));

        return $response
            ->setNextUrl(route('marketplace.vendor.dashboard'))
            ->setMessage(__('Registered successfully!'));
    }

    public function getConnectStripe()
    {
        $customer = auth('customer')->user();

        if (!$customer->is_vendor) {
            return redirect()->route('marketplace.vendor.dashboard');
        }

        $countries = StripeConnectService::getAllowedCountriesList();

        SeoHelper::setTitle(__('Connect Stripe'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Connect Stripe'), route('marketplace.vendor.connect-stripe'));

        return MarketplaceHelper::view('vendor-dashboard.connect-stripe', compact('countries'));
    }

    public function postConnectStripe(Request $request, BaseHttpResponse $response)
    {
        $customer = auth('customer')->user();

        $vendorInfo = $customer->vendorInfo;

        if (!$customer->is_vendor) {
            return redirect()->route('marketplace.vendor.dashboard');
        }

        $request->validate([
            'country' => [Rule::in(array_keys(StripeConnectService::getAllowedCountriesList()))],
            'terms_and_conditions' => 'accepted'
        ]);

        Stripe::setApiKey(setting('payment_stripe_secret'));
        Stripe::setClientId(setting('payment_stripe_client_id'));

        if (empty($vendorInfo->stripe_connect_id)) {
            $stripeAccount = StripeConnectService::createAccount($customer->email);

            $vendorInfo->setAttribute('stripe_connect_id', $stripeAccount->id);
            $vendorInfo->save();
        }

        if (!isset($stripeAccount)) {
            $stripeAccount = StripeConnectService::getAccount($vendorInfo->stripe_connect_id);
        }

        $vendorInfo->setAttribute('stripe_connect_id', $stripeAccount->id);
        $vendorInfo->save();

        if (!$stripeAccount->details_submitted) {
            $link = StripeConnectService::getOnboardingLink($vendorInfo->stripe_connect_id);
        } else {
            $link = StripeConnectService::getLoginLink($vendorInfo->stripe_connect_id);
        }

        return $response->setNextUrl($link);
    }

    protected function approveVendor(Customer $vendor, Request $request)
    {
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
