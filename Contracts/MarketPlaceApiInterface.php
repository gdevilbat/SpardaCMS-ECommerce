<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Contracts;

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
interface MarketPlaceApiInterface
{
	public function getAccessToken($shop_id);

	public function validateRequest(array $request, array $parameter);

	public function getSignature($base_string);

	public function getBaseString($path, array $parameter);

	public function makeRequest(string $url, string $path, array $parameter, string $sign, string $method = 'POST');
}
