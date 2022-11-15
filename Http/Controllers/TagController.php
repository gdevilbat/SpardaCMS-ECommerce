<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Gdevilbat\SpardaCMS\Modules\Post\Http\Controllers\TagController as CoreTag;

class TagController extends CoreTag
{
    public function __construct()
    {
        parent::__construct();
        $this->module = 'ecommerce';
        $this->mod_dir = 'Tag';
        $this->taxonomy = 'tag';

    }
}
