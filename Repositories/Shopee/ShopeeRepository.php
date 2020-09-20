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

	public function __construct()
	{
		$this->item = new API\ItemRepository;
	}
}
