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
interface MarketPlaceLogisticsInterface extends MarketPlaceApiInterface
{
	public function getLogistics(array $request): Object;
}
