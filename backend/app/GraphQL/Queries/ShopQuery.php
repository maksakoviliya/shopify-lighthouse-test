<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Queries\Contracts\ShopifyQuery;
use Illuminate\Support\Arr;

final class ShopQuery extends ShopifyQuery
{
	public function getQuery(): string
	{
		return <<<'GQL'
        {
            shop {
                name
            }
        }
        GQL;
	}

	public function resolve(array $data): array
	{
		return [
			'name' => Arr::get($data, 'shop.name'),
		];
	}
}
