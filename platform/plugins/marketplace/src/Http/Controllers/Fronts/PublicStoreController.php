<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Http\Requests\CheckStoreUrlRequest;
use Botble\Marketplace\Models\Store;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PublicStoreController
{
    public function getStores(Request $request)
    {
        Theme::breadcrumb()->add(__('Home'), route('public.index'));

        SeoHelper::setTitle(__('Stores'))->setDescription(__('Stores'));

        $condition = [];

        $search = BaseHelper::stringify(BaseHelper::clean($request->input('q')));
        if ($search) {
            $condition[] = ['name', 'LIKE', '%' . $search . '%'];
        }

        $with = ['slugable'];
        if (EcommerceHelper::isReviewEnabled()) {
            $with['reviews'] = function ($query) {
                $query->where([
                    'ec_products.status' => BaseStatusEnum::PUBLISHED,
                    'ec_products.is_custom' => 0,
                    'ec_reviews.status' => BaseStatusEnum::PUBLISHED,
                ]);
            };
        }

        $stores = Store::query()
            ->wherePublished()
            ->where($condition)
            ->with($with)
            ->withCount([
                'products' => function ($query) {
                    $query->wherePublished();
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(12);

        return Theme::scope('marketplace.stores', compact('stores'), MarketplaceHelper::viewPath('stores', false))->render();
    }

    public function getStore(
        string $key,
        Request $request,
        GetProductService $productService,
        BaseHttpResponse $response
    ) {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Store::class));

        if (! $slug) {
            abort(404);
        }

        Assets::addScriptsDirectly([
            'vendor/core/plugins/marketplace/js/custom-order.js',
        ]);

        $condition = [
            'mp_stores.id' => $slug->reference_id,
            'mp_stores.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && $request->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $store = Store::query()->with(['slugable', 'metadata'])->where($condition)->firstOrFail();

        if ($store->slugable->key !== $slug->key) {
            return redirect()->to($store->url);
        }

        SeoHelper::setTitle($store->name)->setDescription($store->description);

        $meta = new SeoOpenGraph();
        if ($store->logo) {
            $meta->setImage(RvMedia::getImageUrl($store->logo));
        }
        $meta->setDescription($store->description);
        $meta->setUrl($store->url);
        $meta->setTitle($store->name);

        $subscription = Product::query()
            ->where('store_id', '=', $store->id)
            ->where('price_recurring_interval', '=', 'month')
            ->where('is_custom', '=', 1)
            ->where('status', '=', BaseStatusEnum::PUBLISHED())
            ->first();

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add($store->name, $store->url);

        $with = EcommerceHelper::withProductEagerLoadingRelations();

        $products = $productService->getProduct($request, null, null, $with, [], ['store_id' => $store->id]);

        if ($request->ajax()) {
            $total = $products->total();
            $message = $total > 1 ? __(':total Products found', compact('total')) : __(
                ':total Product found',
                compact('total')
            );

            $view = Theme::getThemeNamespace('views.marketplace.stores.items');

            if (! view()->exists($view)) {
                $view = MarketplaceHelper::viewPath('stores.items', false);
            }

            return $response
                ->setData(view($view, compact('products', 'store'))->render())
                ->setMessage($message);
        }

        return Theme::scope('marketplace.store', compact('store', 'products', 'subscription'), MarketplaceHelper::viewPath('store', false))->render();
    }

    public function checkStoreUrl(CheckStoreUrlRequest $request, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $slug = $request->input('url');
        $slug = Str::slug($slug, '-', ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);

        $existing = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Store::class));

        $response->setData(['slug' => $slug]);

        if ($existing && $existing->reference_id != $request->input('reference_id')) {
            return $response
                ->setError()
                ->setMessage(__('Not Available'));
        }

        return $response->setMessage(__('Available'));
    }
}
