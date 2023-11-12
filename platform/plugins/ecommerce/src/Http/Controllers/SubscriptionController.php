<?php

namespace Botble\Ecommerce\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\ProductForm;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Models\GroupedProduct;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Services\Products\DuplicateProductService;
use Botble\Ecommerce\Services\Products\StoreAttributesOfProductService;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Ecommerce\Services\StoreProductTagService;
use Botble\Ecommerce\Tables\ProductTable;
use Botble\Ecommerce\Tables\ProductVariationTable;
use Botble\Ecommerce\Traits\ProductActionsTrait;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Models\StoreCustomer;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionController extends BaseController
{
    public function __construct()
    {
        Stripe::setApiKey(setting('payment_stripe_secret'));
        Stripe::setClientId(setting('payment_stripe_client_id'));
    }

    public function index()
    {
        SeoHelper::setTitle(__('Subscriptions'));

        $stripeCustomerIds = [];
        foreach (StoreCustomer::query()->get() as $storeCustomer) {
            $stripeCustomerIds[$storeCustomer->stripe_customer_id] = $storeCustomer->store;
        }

        $subscriptionsData = [];

        foreach ($stripeCustomerIds as $stripeCustomerId => $store) {
            $options = [];

            if ($store) {
                $options = ['stripe_account' => $store->customer->vendorInfo->stripe_connect_id];
            }

            $subscriptions = Subscription::all(['customer' => $stripeCustomerId, 'status' => 'all'], $options);

            foreach ($subscriptions as $subscription) {
                $productId = $subscription->items->data[0]->price->product;
                $product = \Stripe\Product::retrieve($productId, $options);

                $subscriptionsData[] = [
                    'id' => $subscription->id,
                    'next_payment' => date('Y-m-d H:i:s', $subscription->current_period_end),
                    'vendor_id' => $store ? $store->customer_id : null,
                    'vendor_name' => $store ? $store->name : 'Marketplace',
                    'stripe_customer_id' => $stripeCustomerId,
                    'price' => strtoupper($subscription->currency) . ' ' . $subscription->items->data[0]->price->unit_amount / 100,
                    'period' => $subscription->items->data[0]->price->recurring->interval,
                    'status' => $subscription->status,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                ];
            }
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Subscriptions'), route('customer.subscriptions'));

        return view('plugins/ecommerce::subscriptions.list', compact('subscriptionsData'));
    }

    public function getViewSubscription(string $id, string|null $vendor_id = null)
    {
        $store = null;
        $options = [];
        if ($vendor_id) {
            $store = Store::query()->where('customer_id', '=', $vendor_id)->first();
            $options = ['stripe_account' => $store->customer->vendorInfo->stripe_connect_id];
        }

        $subscription = Subscription::retrieve($id, $options);
        $product = Product::retrieve($subscription->items->data[0]->price->product, $options);
        $invoices = Invoice::all(['subscription' => $subscription->id], $options);
        $customer = Customer::retrieve($subscription->customer, $options);

        SeoHelper::setTitle(__('Subscription details - :name', ['name' => $product->name]));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(
                __('Subscription details - :name', ['name' => $product->name]),
                route('customer.subscriptions.view', ['id' => $id, 'vendor_id' => $vendor_id])
            );

        return view('plugins/ecommerce::subscriptions.view', compact('subscription', 'product', 'customer', 'invoices', 'store'));

    }

    public function getCancelSubscription(int|string $id, BaseHttpResponse $response, string | null $vendor_id = null)
    {
        $options = [];
        if ($vendor_id) {
            $store = Store::query()->where('customer_id', '=', $vendor_id)->first();
            $options = ['stripe_account' => $store->customer->vendorInfo->stripe_connect_id];
        }

        Subscription::retrieve($id, $options)->cancel();

        return $response->setNextUrl(route('subscriptions.view', ['id' => $id, 'vendor_id' => $vendor_id]));
    }

}
