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
interface MarketPlaceItemInterface extends MarketPlaceApiInterface
{
	public function getItemsList(array $request): Object;

	public function getItemDetail(array $request): Object;

	public function getVariations(array $request): Object;

	public function addItem(array $request): Object;

	public function itemUpdate(array $request): Object;

	public function itemUpdatePrice(array $request): Object;

	public function itemUpdateStock(array $request): Object;

	public function itemInitTierVariations(array $request): Object;

	public function itemAddTierVariations(array $request): Object;

	public function itemUpdateTierVariationList(array $request): Object;

	public function itemUpdateTierVariationIndex(array $request): Object;

	public function itemUpdateVariationPriceBatch(array $request): Object;

	public function itemUpdateVariationStockBatch(array $request): Object;

	public function getCategories(array $request): Object;

	public function getAttributes(array $request): Object;
}
