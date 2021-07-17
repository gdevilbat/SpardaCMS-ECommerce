<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceShopInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceItemInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceLogisticsInterface;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceImageInterface;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
interface MarketPlaceInterface
{
	public function __construct(MarketPlaceShopInterface $shop, MarketPlaceItemInterface $item, MarketPlaceLogisticsInterface $logistics, MarketPlaceImageInterface $image);
}
