<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\Shopee;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class ShopeeRepository
{
	public $item;
	public $shop;

	public function __construct()
	{
		$this->item = new API\ItemRepository;
		$this->shop = new API\ShopRepository;
		$this->image = new API\ImageRepository;
		$this->logistics = new API\LogisticsRepository;
	}
}
