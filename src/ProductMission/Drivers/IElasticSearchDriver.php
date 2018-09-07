<?php
namespace ProductMission\Drivers;

interface IElasticSearchDriver
{
	
	/**
	 * Finds product by id and returns an array of product data.
	 * @param string $id
	 * @return array
	 */
	public function findById(string $id): array;
	
}