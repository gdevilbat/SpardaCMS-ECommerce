<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts;

use Illuminate\Support\Facades\Facade;

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
interface MarketPlaceDiscountInterface extends MarketPlaceApiInterface
{
	public function getDiscountsList(array $request): Object;
}
