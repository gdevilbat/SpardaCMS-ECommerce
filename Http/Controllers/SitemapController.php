<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;


class SitemapController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data['posts'] = Product::where(['post_type' => 'product', 'post_status' => 'publish'])
                                    ->get();

        return response()->view('ecommerce::sitemap', $data)
                        ->header('Content-Type', 'text/xml');
    }
}
