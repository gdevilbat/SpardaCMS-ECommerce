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
interface MarketPlaceItemInterface extends MarketPlaceApiInterface
{
	public function getItemsList(Request $request): Object;

	public function getItemDetail(Request $request): Object;

	public function getVariations(Request $request): Object;

	public function addItem(Request $request): Object;

	public function itemUpdate(Request $request): Object;

	public function itemUpdatePrice(Request $request): Object;

	public function itemUpdateStock(Request $request): Object;

	public function itemInitTierVariations(Request $request): Object;

	public function itemAddTierVariations(Request $request): Object;

	public function itemUpdateTierVariationList(Request $request): Object;

	public function itemUpdateTierVariationIndex(Request $request): Object;

	public function itemUpdateVariationPriceBatch(Request $request): Object;

	public function itemUpdateVariationStockBatch(Request $request): Object;

	public function getCategories(Request $request): Object;

	public function getAttributes(Request $request): Object;
}
