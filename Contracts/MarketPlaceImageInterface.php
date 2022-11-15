<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts;

use Illuminate\Support\Facades\Facade;

use Illuminate\Http\Request;

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
interface MarketPlaceImageInterface extends MarketPlaceApiInterface
{
	public function uploadImage(array $request): Object;
}
