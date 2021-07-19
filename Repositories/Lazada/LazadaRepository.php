<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceShopInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceItemInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceLogisticsInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceImageInterface;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class LazadaRepository implements MarketPlaceInterface
{
	public function __construct(MarketPlaceShopInterface $shop, MarketPlaceItemInterface $item, MarketPlaceLogisticsInterface $logistics, MarketPlaceImageInterface $image)
	{
		$this->shop = $shop;
		$this->item = $item;
		$this->logistics = $logistics;
		$this->image = $image;
	}
}