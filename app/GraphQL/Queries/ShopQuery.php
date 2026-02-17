<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;

final readonly class ShopQuery
{
	public function __construct(
		protected ShopifyService $shopify
	) {
	}

	/**
	 * @throws ConnectionException
	 */
	public function __invoke(): array
	{
		$query = <<<'GQL'
        {
            shop {
                name
            }
        }
        GQL;

		$data = $this->shopify->graphql($query);

		return [
			'name' => $data['shop']['name'] ?? null,
		];
	}
}
