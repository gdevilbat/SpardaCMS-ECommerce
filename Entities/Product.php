<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;

class Product extends Post
{
    public function productMeta()
    {
        return $this->hasOne('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey());
    }
}
