<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;

class Product extends Post
{
    public function productMeta()
    {
        return $this->hasOne('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey());
    }

    public function getTokopediaSlugAttribute()
    {
    	if(!empty($this->postMeta->where('meta_key', 'tokopedia_slug')->first()))
            return json_decode(json_encode($this->postMeta->where('meta_key', 'tokopedia_slug')->first()->meta_value));

        return null;
    }
}
