<?php
namespace ProductMission\Caching;

interface ICacheStorage
{
	
	/**
	 * Finds product by id and returns an array of product data.
	 * Returns an empty array if product is not found.
	 * @param string $id
	 * @return array
	 */
	public function findProduct(string $id): array;
	
	
	/**
	 * Saves an array of product data under product id.
	 * @param string $id
	 * @param array $productData
	 */
	public function saveProduct(string $id, array $productData): void;
	
}