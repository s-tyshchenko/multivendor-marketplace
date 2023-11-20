<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Ecommerce\Enums\GlobalOptionEnum;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\ProductCategoryHelper;
use Botble\Ecommerce\Forms\Fields\CategoryMultiField;
use Botble\Ecommerce\Forms\ProductForm as BaseProductForm;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\GlobalOption;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductCollection;
use Botble\Ecommerce\Models\ProductLabel;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\Tax;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Forms\Fields\CustomEditorField;
use Botble\Marketplace\Forms\Fields\CustomImagesField;
use Botble\Marketplace\Http\Requests\ProductRequest;
use Botble\Marketplace\Models\Store;
use Botble\Marketplace\Tables\ProductVariationTable;

class ProductForm extends BaseProductForm
{
    public function buildForm(): void
    {
        $this->addAssets();

        $productId = null;
        $store = Store::query()->where('customer_id', '=', auth('customer')->id())->first();
        $isFirstProduct = $store && !Product::query()->where('store_id', '=', $store->id)->exists();
        $tags = null;

        if ($this->getModel()) {
            $productId = $this->getModel()->id;
        }

        $this
            ->setupModel(new Product())
            ->withCustomFields()
            ->addCustomField('customEditor', CustomEditorField::class)
            ->addCustomField('customImages', CustomImagesField::class)
            ->addCustomField('multiCheckList', MultiCheckListField::class)
            ->addCustomField('tags', TagField::class)
            ->setFormOption('template', MarketplaceHelper::viewPath('vendor-dashboard.forms.base'))
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(ProductRequest::class)
            ->setActionButtons(MarketplaceHelper::view('vendor-dashboard.forms.actions')->render())
            ->add('name', 'text', [
                'label' => trans('plugins/ecommerce::products.form.name'),
                'label_attr' => ['class' => 'text-title-field required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 150,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'attr' => [
                    'rows' => 2,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 1000,
                ],
            ])
            ->add('content', 'textarea', [
                'label' => trans('core/base::forms.content'),
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->add('images', 'customImages', [
                'label' => trans('plugins/ecommerce::products.form.image'),
                'values' => $productId ? $this->getModel()->images : [],
            ])
            ->add('product_type', 'hidden', [
                'value' => ProductTypeEnum::DIGITAL,
            ])
            ->addMetaBoxes([
                'general' => [
                    'title' => trans('plugins/ecommerce::products.pricing'),
                    'content' => view(
                        'plugins/ecommerce::products.partials.general',
                        [
                            'product' => $productId ? $this->getModel() : null,
                            'isVariation' => false,
                            'originalProduct' => null,
                        ]
                    ),
                    'before_wrapper' => '<div id="main-manage-product-type">',
                    'after_wrapper' => '</div>',
                    'priority' => 2,
                ],
            ]);

        if ($isFirstProduct) {
            $this->addMetaBoxes([
                'terms-and-conditions' => [
                    'content' => view('plugins/ecommerce::products.partials.terms-and-conditions')
                ]
            ]);
        }

    }
}
