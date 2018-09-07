<?php
namespace ProductMission\Statistics;

interface IProductStatisticsStorage
{
	
	/**
	 * Increases search count for product.
	 * Returns an empty array if product is not found.
	 * @param string $id
	 */
	public function increaseProductSearchCount(string $id): void;
	
}