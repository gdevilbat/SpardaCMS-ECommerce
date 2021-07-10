<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories;

use Gdevilbat\SpardaCMS\Modules\Post\Repositories\AbstractRepository;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ProductRepository extends AbstractRepository
{
	public function __construct(\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post $model, \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository $acl)
    {
        parent::__construct($model, $acl);
        $this->setModule('ecommerce');
        $this->setPostType('product');
    }

    public function save(Request $request, $callback = null)
    {
    	return parent::save($request, 'productMetaStore');
    }

    public function validatePost(Request $request)
    {
        $validator = parent::validatePost($request);

        $validator->addRules([
                'taxonomy.category' => 'required',
        ]);

        $validator->addRules([
                'meta.gallery.*.photo' => 'required',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.merchant' => 'required_with:meta.'.ProductMeta::TOKPED_SUPPLIER.'.slug',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.slug' => 'required_with:meta.'.ProductMeta::TOKPED_SUPPLIER.'.merchant',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.product_id' => 'required_with:meta.'.ProductMeta::TOKPED_SUPPLIER.'.slug',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.children.*.product_id' => 'required',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.children' => 'required_if:meta.'.ProductMeta::TOKPED_SUPPLIER.'.is_variant,true',
                'meta.'.ProductMeta::TOKPED_SUPPLIER.'.product_id' => 'required_if:meta.'.ProductMeta::TOKPED_SUPPLIER.'.is_variant,true',
                'meta.'.ProductMeta::SHOPEE_SUPPLIER.'.shop_id' => 'required_with:meta.'.ProductMeta::SHOPEE_SUPPLIER.'.product_id',
                'meta.'.ProductMeta::SHOPEE_SUPPLIER.'.product_id' => 'required_with:meta.'.ProductMeta::SHOPEE_SUPPLIER.'.shop_id',
                'meta.'.ProductMeta::SHOPEE_SUPPLIER.'.children.*.product_id' => 'required|required_with:meta.'.ProductMeta::SHOPEE_SUPPLIER.'.product_id',
                'meta.'.ProductMeta::SHOPEE_SUPPLIER.'.children' => 'required_if:meta.'.ProductMeta::SHOPEE_SUPPLIER.'.is_variant,true',
                'meta.'.ProductMeta::SHOPEE_SUPPLIER.'.product_id' => 'required_if:meta.'.ProductMeta::SHOPEE_SUPPLIER.'.is_variant,true',
                'meta.'.ProductMeta::TOKPED_STORE.'.merchant' => 'required_with:meta.'.ProductMeta::TOKPED_STORE.'.slug',
                'meta.'.ProductMeta::TOKPED_STORE.'.slug' => 'required_with:meta.'.ProductMeta::TOKPED_STORE.'.merchant',
                'meta.'.ProductMeta::TOKPED_STORE.'.product_id' => 'required_with:meta.'.ProductMeta::TOKPED_STORE.'.slug',
                'meta.'.ProductMeta::TOKPED_STORE.'.children.*.product_id' => 'required|required_with:meta.'.ProductMeta::TOKPED_STORE.'.slug',
                'meta.'.ProductMeta::TOKPED_STORE.'.children' => 'required_if:meta.'.ProductMeta::TOKPED_STORE.'.is_variant,true',
                'meta.'.ProductMeta::TOKPED_STORE.'.product_id' => 'required_if:meta.'.ProductMeta::TOKPED_STORE.'.is_variant,true',
                'meta.'.ProductMeta::SHOPEE_STORE.'.shop_id' => 'required_with:meta.'.ProductMeta::SHOPEE_STORE.'.product_id',
                'meta.'.ProductMeta::SHOPEE_STORE.'.product_id' => 'required_with:meta.'.ProductMeta::SHOPEE_STORE.'.shop_id',
                'meta.'.ProductMeta::SHOPEE_STORE.'.children.*.product_id' => 'required|required_with:meta.'.ProductMeta::SHOPEE_STORE.'.product_id',
                'meta.'.ProductMeta::SHOPEE_STORE.'.children' => 'required_if:meta.'.ProductMeta::SHOPEE_STORE.'.is_variant,true',
                'meta.'.ProductMeta::SHOPEE_STORE.'.product_id' => 'required_if:meta.'.ProductMeta::SHOPEE_STORE.'.is_variant,true',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.variants.*.name' => 'required',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.variants.*.option' => 'required',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.variants.*.option.*.value' => 'required',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.children.*.name' => 'required',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.children.*.price' => 'required',
                'meta.'.ProductMeta::PRODUCT_VARIANT.'.children.*.stock.*' => 'required',
                'taxonomy.category' => 'required',
                'product_meta.product_price' => 'required|max:11',
                'product_meta.product_stock' => 'required|numeric|min:0|digits_between:1,5',
                'product_meta.availability' => [
                        //'required',
                        Rule::in(['in stock', 'out of stock', 'preorder', 'available for order', 'discontinued'])
                    ],
                'product_meta.condition' => [
                        'required',
                        Rule::in(['new', 'refurbished', 'used'])
                    ]
        ]);

        if(!empty($request->input('product_meta.product_sale')))
        {
            $validator->addRules([
                    'product_meta.product_sale' => [
                        'max:11',
                        function ($attribute, $value, $fail) use ($request) {
                            if ($this->pricetTagToInteger($value) > $this->pricetTagToInteger($request->input('product_meta.product_price'))) {
                                $fail($attribute.' Must Be Smaller Than Product Price.');
                            }
                        },
                ],      
            ]);
        }

        return $validator;
    }

    public function productMetaStore(Request $request, \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post $post)
    {
        if($request->isMethod('POST'))
        {
            $product_meta = new ProductMeta;
            $product_meta[\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey()] = $post->getKey();
        }
        else
        {
            $product_meta = ProductMeta::find(decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey())));
            if(empty($product_meta))
            {
                $product_meta = new ProductMeta;
                $product_meta[\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey()] = $post->getKey();
            }
        }

        foreach ($request->input('product_meta') as $key => $value) 
        {
            $product_meta->$key = $value;
        }

        if(!$request->has('meta.'.ProductMeta::PRODUCT_VARIANT))
        {
            PostMeta::where(['meta_key' => ProductMeta::PRODUCT_VARIANT, Product::FOREIGN_KEY => $post->getKey()])->delete();
        }

        $product_meta->save();
    }

    public function getCategory()
    {
        return 'product-category';
    }

    public function getTag()
    {
        return 'tag';
    }

    public function pricetTagToInteger($value)
    {
        return preg_replace('/[,_]/', '',$value);
    }
}
