<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Post\Foundation\AbstractPost;

class ProductController extends AbstractPost
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();

        $this->module = 'ecommerce';
        $this->post_type = 'product';
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view($this->getModule().'::show');
    }

    public function getCategory()
    {
        return 'product-category';
    }

    public function getTag()
    {
        return 'tag';
    }
}
