<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class ShopifyService
{
	protected string $shop;
	protected string $clientId;
	protected string $clientSecret;

	public function __construct()
	{
		$this->shop = config('services.shopify.shop');
		$this->clientId = config('services.shopify.client_id');
		$this->clientSecret = config('services.shopify.client_secret');

		if (!$this->shop || !$this->clientId || !$this->clientSecret) {
			throw new RuntimeException('Set SHOPIFY_SHOP, SHOPIFY_CLIENT_ID and SHOPIFY_CLIENT_SECRET in .env');
		}
	}

	protected function getToken(): string
	{
		return Cache::remember('shopify_token', 300, function () {
			$response = Http::asForm()->post(
				"https://$this->shop/admin/oauth/access_token",
				[
					'grant_type' => 'client_credentials',
					'client_id' => $this->clientId,
					'client_secret' => $this->clientSecret,
				]
			);

			if (!$response->successful()) {
				throw new RuntimeException(
					"Token request failed: {$response->status()} - {$response->body()}"
				);
			}

			return $response->json('access_token');
		});
	}

	/**
	 * @throws ConnectionException
	 */
	public function graphql(string $query, array $variables = []): array
	{
		$response = Http::withHeaders([
			'X-Shopify-Access-Token' => $this->getToken(),
			'Content-Type' => 'application/json',
		])->withBody(
			json_encode([
				'query' => $query,
				'variables' => (object) $variables,
			]),
		)->post("https://$this->shop/admin/api/2025-01/graphql.json");

		if (!$response->successful()) {
			throw new RuntimeException(
				"GraphQL request failed: {$response->status()} - {$response->body()}"
			);
		}

		$body = $response->json();
		if (!empty($body['errors'])) {
			throw new RuntimeException('GraphQL errors: ' . json_encode($body['errors']));
		}

		return $body['data'] ?? [];
	}
}
