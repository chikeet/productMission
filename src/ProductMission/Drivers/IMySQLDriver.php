<?php
namespace ProductMission\Drivers;

interface IMySQLDriver
{
	
	/**
	 * Finds product by id and returns an array of product data.
	 * @param string $id
	 * @return array
	 */
	public function findProduct(string $id): array;
	
}