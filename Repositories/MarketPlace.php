<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories;

use Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceDriver;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\ShopeeRepository;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\TokopediaRepository;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\LazadaRepository;

use Illuminate\Support\Manager;
use InvalidArgumentException;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class MarketPlace extends Manager implements MarketPlaceDriver
{
	/**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }
    
    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createShopeeDriver()
    {
        return $this->buildProvider(
            ShopeeRepository::class, [
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API\ShopRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API\ItemRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API\LogisticsRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API\ImageRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee\API\DiscountRepository,
            ]
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createTokopediaDriver()
    {
        return $this->buildProvider(
            TokopediaRepository::class, [
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API\ShopRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API\ItemRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API\LogisticsRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Tokopedia\API\ImageRepository,
            ]
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createLazadaDriver()
    {
        return $this->buildProvider(
            LazadaRepository::class, [
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API\ShopRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API\ItemRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API\LogisticsRepository,
                new \Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Lazada\API\ImageRepository,
            ]
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, array $dependecy = [])
    {
        $instance = new $provider(...$dependecy);

        if(!($instance instanceof \Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts\MarketPlaceInterface))
            throw new InvalidArgumentException('Driver does not implement contract correctly');

        return $instance;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
