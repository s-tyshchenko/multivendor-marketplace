<?php

namespace Botble\Ecommerce\Models\Custom;

use Botble\Ecommerce\Models\Product as BaseProduct;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends BaseProduct
{
    public function originalProduct(): Attribute
    {
        return Attribute::make(get: function (): self {
            return $this;
        });
    }
}
