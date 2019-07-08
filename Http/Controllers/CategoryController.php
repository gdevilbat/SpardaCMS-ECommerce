<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Gdevilbat\SpardaCMS\Modules\Post\Http\Controllers\CategoryController as CoreCategory;

class CategoryController extends CoreCategory
{
    public function __construct()
    {
        parent::__construct();
        $this->module = 'ecommerce';
        $this->mod_dir = 'Category';
        $this->taxonomy = 'product-category';

    }
}
