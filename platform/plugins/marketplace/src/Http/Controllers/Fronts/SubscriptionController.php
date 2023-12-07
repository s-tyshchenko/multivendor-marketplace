<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\InvoiceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\UpdateOrderRequest;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Forms\ProductForm;
use Botble\Marketplace\Forms\SubscriptionForm;
use Botble\Marketplace\Tables\OrderTable;
use Botble\Payment\Models\Payment;
use Botble\SeoHelper\Facades\SeoHelper;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
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

    public function index(BaseHttpResponse $response)
    {
        PageTitle::setTitle(__('Subscriptions'));

        $vendor = auth('customer')->user();

        $subscriptionsData = [];

        if ($vendor->vendorInfo->stripe_connect_id) {
            $options = ['stripe_account' => $vendor->vendorInfo->stripe_connect_id];
            $subscriptions = Subscription::all(['status' => 'all'], $options);

            foreach ($subscriptions as $subscription) {
                $productId = $subscription->items->data[0]->price->product;
                $product = Product::retrieve($productId, $options);
                $customer = Customer::retrieve($subscription->customer, $options);

                $subscriptionsData[] = [
                    'id' => $subscription->id,
                    'next_payment' => date('Y-m-d H:i:s', $subscription->current_period_end),
                    'customer_name' => $customer->name,
                    'price' => strtoupper($subscription->currency) . ' ' . $subscription->items->data[0]->price->unit_amount / 100,
                    'period' => $subscription->items->data[0]->price->recurring->interval,
                    'status' => $subscription->status,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                ];
            }
        }

        return MarketplaceHelper::view('vendor-dashboard.subscriptions.list', compact('subscriptionsData'));
    }

    public function getViewSubscription(string $id)
    {
        $vendor = auth('customer')->user();

        $options = ['stripe_account' => $vendor->vendorInfo->stripe_connect_id];

        $subscription = Subscription::retrieve($id, $options);
        $product = Product::retrieve($subscription->items->data[0]->price->product, $options);
        $invoices = Invoice::all(['subscription' => $subscription->id], $options);
        $customer = Customer::retrieve($subscription->customer, $options);

        $order = null;
        $orderProduct = null;

        if (isset($subscription->metadata['order_id'])) {
            $orderId = json_decode($subscription->metadata['order_id']);
            $order = Order::query()->where('id', '=', $orderId[0])->firstOrFail();
            $orderProduct = OrderProduct::query()->where('order_id', '=', $orderId[0])->firstOrFail();
        }

        SeoHelper::setTitle(__('Subscription details - :name', ['name' => $product->name]));

        return MarketplaceHelper::view('vendor-dashboard.subscriptions.view', compact('subscription', 'customer', 'product', 'invoices', 'order', 'orderProduct'));
    }


    public function getCancelSubscription(string $id, BaseHttpResponse $response)
    {
        $vendor = auth('customer')->user();
        $options = ['stripe_account' => $vendor->vendorInfo->stripe_connect_id];

        Subscription::retrieve($id, $options)->cancel();

        return $response->setNextUrl(route('marketplace.vendor.subscriptions.view', ['id' => $id]));
    }

    public function getCreateSubscription(FormBuilder $formBuilder)
    {
        $store = auth('customer')->user()->store;
        $subscription = \Botble\Ecommerce\Models\Product::query()
            ->where('store_id', '=', $store->id)
            ->where('price_recurring_interval', '=', 'month')
            ->where('is_custom', '=', 1)
            ->firstOrNew([
                'name' => 'Subscription',
                'is_custom' => 1,
                'price_recurring_interval' => 'month',
                'sale_type' => 0,
                'store_id' => $store->id
            ]);

        $subscription->save();

        PageTitle::setTitle(trans('plugins/ecommerce::products.edit', ['name' => $subscription->name]));

        $route = !$subscription->id ?
            route('marketplace.vendor.products.create') :
            route('marketplace.vendor.products.edit', $subscription->id);

        return $formBuilder
            ->create(SubscriptionForm::class, ['url' => $route, 'model' => $subscription])
            ->renderForm();
    }

}
