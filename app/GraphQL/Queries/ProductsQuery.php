<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;

final readonly class ProductsQuery
{
	public function __construct(protected ShopifyService $shopifyService)
	{
	}

	/**
	 * @throws ConnectionException
	 */
	public function __invoke(): array
	{
		$query = <<<GQL
        {
            products(first: 3) {
                edges {
                    node {
                        id
                        title
                        handle
                    }
                }
            }
        }
        GQL;

		$data = $this->shopifyService->graphql($query);
		return collect($data['products']['edges'] ?? [])->map(fn($e) => $e['node'])->toArray();
	}
}
