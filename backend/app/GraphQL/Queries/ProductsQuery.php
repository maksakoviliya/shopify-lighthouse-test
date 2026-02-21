<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Queries\Contracts\ShopifyQuery;
use Illuminate\Support\Arr;

final class ProductsQuery extends ShopifyQuery
{
	public function getQuery(): string
	{
		return <<<'GQL'
        query GetProducts($first: Int!, $after: String) {
            products(first: $first, after: $after) {
                edges {
                    node {
                        id
                        title
                        description
                    }
                }
                pageInfo {
                    hasNextPage
                    endCursor
                }
            }
        }
        GQL;
	}

	protected function getVariables(array|null $args): array
	{
		$variables = [
			'first' => Arr::get($args, 'first', 10)
		];

		if (!empty($args['after'])) {
			$variables['after'] = $args['after'];
		}

		return $variables;
	}

	public function resolve(array $data): array
	{
		return collect(Arr::get($data, 'products.edges', []))
			->map(fn($item) => Arr::get($item, 'node'))
			->toArray();
	}
}
