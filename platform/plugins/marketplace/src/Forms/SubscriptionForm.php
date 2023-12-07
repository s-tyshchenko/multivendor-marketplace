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

class SubscriptionForm extends BaseProductForm
{
    public function buildForm(): void
    {
        $this->addAssets();

        $productId = null;
        $tags = null;

        if ($this->getModel()) {
            $productId = $this->getModel()->id;
        }

        $this
            ->setupModel(new Product())
            ->withCustomFields()
            ->addCustomField('customEditor', CustomEditorField::class)
            ->addCustomField('multiCheckList', MultiCheckListField::class)
            ->addCustomField('tags', TagField::class)
            ->setFormOption('template', MarketplaceHelper::viewPath('vendor-dashboard.forms.base'))
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(ProductRequest::class)
            ->setActionButtons(MarketplaceHelper::view('vendor-dashboard.forms.actions')->render())
            ->add('name', 'hidden', [
                'label' => trans('core/base::forms.name'),
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => ["published"=> "Active", "draft" => "Inactive"]
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'attr' => [
                    'rows' => 2,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 1000,
                ],
            ])
            ->add('product_type', 'hidden', [
                'value' => ProductTypeEnum::DIGITAL,
            ])
            ->addMetaBoxes([
                'general' => [
                    'title' => trans('plugins/ecommerce::products.pricing'),
                    'content' => view(
                        'plugins/ecommerce::products.partials.subscription',
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

    }
}
