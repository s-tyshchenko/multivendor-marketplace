<div class="archive-header-2 text-center pt-80 pb-50 mb-10"
     style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($store->getMetaData('cover_image', true)) }}');
         background-size: cover;
         background-position: center;
         background-repeat: no-repeat;
         border-radius: 0 0 20px 20px">
    <h1 class="display-2 mb-10">{{ $store->name }}</h1>
    <p class="mb-20">{{ $store->url }}</p>
    <div class="row">
        <div class="col-12 mb-50">
            @php
                $description = BaseHelper::clean($store->description);
                $content = BaseHelper::clean($store->content);
            @endphp

            @if ($description || $content)
                {!! $content ?: $description !!}
            @endif
        </div>

        <div class="col-lg-5 mx-auto">
            <div class="sidebar-widget-2 widget_search mb-50">
                <div class="search-form form-group">
                    <input name="q" value="{{ BaseHelper::stringify(request()->input('q')) }}" type="text"
                           placeholder="{{ __('Search in this store...') }}">
                    <button type="submit"><i class="fi-rs-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row flex-row-reverse">
    <div class="col-lg-4-5 products-listing position-relative">
        @include(Theme::getThemeNamespace('views.marketplace.stores.items'), compact('products'))
    </div>
    <div class="col-lg-1-5 primary-sidebar sticky-sidebar">
        <div class="sidebar-widget widget-store-info mb-30 bg-3 border-0">
            <div class="vendor-logo mb-30">
                <img src="{{ RvMedia::getImageUrl($store->logo, 'medium', false, RvMedia::getDefaultImage()) }}"
                     alt="{{ $store->name }}"/>
            </div>
            <div class="vendor-info">
                <div class="product-category">
                    <span class="text-muted">{{ __('Since :year', ['year' => $store->created_at->year]) }}</span>
                </div>
                <h4 class="mb-5"><a href="{{ $store->url }}"
                                    class="text-heading">{!! BaseHelper::clean($store->name) !!}</a></h4>

                <div class="vendor-des">
                    <button type="button" data-toggle="modal" data-target="#create-custom-order-modal"
                            class="btn btn-primary">{{ __('Custom order') }}</button>
                </div>

                <div class="vendor-des mb-30">
                    <p class="font-sm text-heading">{!! BaseHelper::clean($store->description) !!}</p>
                </div>

                @include(Theme::getThemeNamespace('views.marketplace.stores.partials.socials'))

                <div class="vendor-info">
                    @include(Theme::getThemeNamespace('views.marketplace.stores.partials.info'))
                </div>
            </div>
        </div>
        @include(Theme::getThemeNamespace('views.marketplace.stores.partials.sidebar'))
    </div>
</div>
