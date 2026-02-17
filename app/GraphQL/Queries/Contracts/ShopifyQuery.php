<?php

namespace App\GraphQL\Queries\Contracts;

use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;

abstract class ShopifyQuery
{
	public function __construct(protected ShopifyService $shopifyService)
	{
	}
	
	abstract function getQuery(): string;
	
	abstract function resolve(array $data): array;

	protected function getVariables(array|null $args): array
	{
		return [];
	}

	/**
	 * @throws ConnectionException
	 */
	public function __invoke($rootValue, array $args): array
	{
		$data = $this->shopifyService->graphql(
			$this->getQuery(),
			$this->getVariables($args)
		);
		
		return $this->resolve($data);
	}
}