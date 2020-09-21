<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;

use Log;

class ShopeeController
{
    protected $shopeeRepository;

    public function __construct()
    {
        $this->shopeeRepository = new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository;
    }

    public function shopGetDetail(Request $request)
    {
        return $this->shopeeRepository->shop->getShopDetail($request->all());
    }

    public function itemGetList(Request $request)
    {
        return $this->shopeeRepository->item->getItemsList($request->all());
    }

    public function itemGetDetail(Request $request)
    {
        return $this->shopeeRepository->item->getItemDetail($request->all());
    }

    public function itemUpdate(Request $request)
    {
        return $this->shopeeRepository->item->itemUpdate($request->all());
    }

    public function itemGetBoosted(Request $request)
    {
        return $this->shopeeRepository->item->getBoostedItem($request->all());
    }
}
