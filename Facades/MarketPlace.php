<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Facades;

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
class MarketPlace extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceDriver::class;
    }
}
