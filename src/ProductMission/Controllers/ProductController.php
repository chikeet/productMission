<?php

namespace ProductMission\Controllers;

use ProductMission\Caching\ICacheStorage;
use ProductMission\Statistics\IProductStatisticsStorage;
use ProductMission\Drivers\IElasticSearchDriver;
use ProductMission\Drivers\IMySQLDriver;

class ProductController
{
	
	/**
	 * @var IElasticSearchDriver
	 */
	protected $elasticSearchDriver;
	
	/**
	 * @var IMySQLDriver
	 */
	protected $mySQLDriver;
	
	/**
	 * @var ICacheStorage
	 */
	protected $cacheStorage;
	
	/**
	 * @var IProductStatisticsStorage
	 */
	private $productStatisticsStorage;
	
	
	public function __construct(
		IElasticSearchDriver $elasticSearchDriver,
		IMySQLDriver $mySQLDriver,
		ICacheStorage $cacheStorage,
		IProductStatisticsStorage $productStatisticsStorage
	)
	{
		$this->elasticSearchDriver = $elasticSearchDriver;
		$this->mySQLDriver = $mySQLDriver;
		$this->cacheStorage = $cacheStorage;
		$this->productStatisticsStorage = $productStatisticsStorage;
	}
	
	
	/**
	 * Returns product data as JSON by product id.
	 * @param string $id
	 * @return string
	 */
	public function detail(string $id): string
	{
		// TODO: sanitize id?
		
		/* Get product */
		$productData = $this->getProductDataFromCache($id);
		if(!$productData){
			$productData = $this->getProductDataFromDataSource($id);
			$this->saveProductDataToCache($id, $productData);
		}
		
		/* Increase search count */
		$this->increaseProductSearchNumber($id);
		
		/* Return JSON */
		return json_encode($productData);
	}
	
	
	/* Getting data from data source ******************************************/
	
	protected function getProductDataFromDataSource(string $id): array
	{
		$productData = $this->elasticSearchDriver->findById($id);
		if(!$productData){
			$productData = $this->mySQLDriver->findProduct($id);
		}
		
		return $productData;
	}
	
	
	/* Search statistics ******************************************************/
	
	protected function increaseProductSearchNumber(string $id): void
	{
		$this->productStatisticsStorage->increaseProductSearchCount($id);
	}
	
	
	/* Caching ****************************************************************/
	
	protected function getProductDataFromCache(string $id): array
	{
		return $this->cacheStorage->findProduct($id);
	}
	
	
	private function saveProductDataToCache(string $id, array $productData): void
	{
		$this->cacheStorage->saveProduct($id, $productData);
	}
	
}