<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;

class Product extends Post
{
    public function productMeta()
    {
        return $this->hasOne('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey());
    }

    public function getTokopediaStoreAttribute()
    {
        if(!empty($this->postMeta->where('meta_key', 'tokopedia_store')->first()))
            return json_decode(json_encode($this->postMeta->where('meta_key', 'tokopedia_store')->first()->meta_value));

        return null;
    }

    public function getTokopediaSlugAttribute()
    {
    	if(!empty($this->postMeta->where('meta_key', 'tokopedia_slug')->first()))
            return json_decode(json_encode($this->postMeta->where('meta_key', 'tokopedia_slug')->first()->meta_value));

        return null;
    }

    public function getTokopediaSupplierAttribute()
    {
        if(!empty($this->postMeta->where('meta_key', 'tokopedia_supplier')->first()))
            return json_decode(json_encode($this->postMeta->where('meta_key', 'tokopedia_supplier')->first()->meta_value));

        return null;
    }

    public function getTokopediaSourceAttribute()
    {
        if(!empty($this->postMeta->where('meta_key', 'tokopedia_source')->first()))
            return json_decode(json_encode($this->postMeta->where('meta_key', 'tokopedia_source')->first()->meta_value));

        return null;
    }

    public function scopeFilterSort($query, \Illuminate\Http\Request $request)
    {
        if(count($request->input()) > 0)
        {
            $query = $query->leftJoin(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getTableName(), function($join){
                                $join->on(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getTableName().'.'.\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey(), '=', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getTableName().'.'.'product_id');
                            });

            if($request->has('lowest_price') && $request->has('highest_price'))
            {
                $query = $query->where('product_price', '>=', $request->input('lowest_price'))
                               ->where('product_price', '<=', $request->input('highest_price'));
            }

            if($request->has('order_by') && $request->has('order_mode'))
            {
                $query = $query->orderBy($request->input('order_by'), $request->input('order_mode'));
            }
        }

        return $query;
    }
}
