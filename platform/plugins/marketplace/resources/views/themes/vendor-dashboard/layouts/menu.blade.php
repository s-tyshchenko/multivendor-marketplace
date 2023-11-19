@php
    $menus = collect([
        [
            'key' => 'marketplace.vendor.dashboard',
            'icon' => 'icon-home',
            'name' => __('Store'),
            'order' => 1,
        ],
        [
            'key' => 'marketplace.vendor.products.index',
            'icon' => 'icon-database',
            'name' => __('Products'),
            'routes' => ['marketplace.vendor.products.create', 'marketplace.vendor.products.edit'],
            'order' => 2,
        ],
        [
            'key' => 'marketplace.vendor.orders.index',
            'icon' => 'icon-bag2',
            'name' => __('Orders'),
            'routes' => ['marketplace.vendor.orders.edit'],
            'order' => 3,
        ],
        [
            'key' => 'marketplace.vendor.subscriptions.index',
            'icon' => 'icon-credit-card',
            'name' => __('Subscriptions'),
            'routes' => ['marketplace.vendor.subscriptions.edit'],
            'order' => 3,
        ]
    ]);

    $currentRouteName = Route::currentRouteName();
@endphp

<ul class="menu">
    @foreach ($menus->sortBy('order') as $item)
        <li>
            <a
                href="{{ route($item['key']) }}"
                @if ($currentRouteName == $item['key'] || in_array($currentRouteName, Arr::get($item, 'routes', []))) class="active" @endif
            >
                <i class="{{ $item['icon'] }}"></i>{{ $item['name'] }}
            </a>
        </li>
    @endforeach
</ul>
