<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/ProductMission/Controllers/ProductController.php';

use ProductMission\Caching\ICacheStorage;
use ProductMission\Drivers\IElasticSearchDriver;
use ProductMission\Drivers\IMySQLDriver;
use ProductMission\Statistics\IProductStatisticsStorage;
use Tester\Assert;
use Tester\TestCase;
use ProductMission\Controllers\ProductController;

class ProductControllerTest extends TestCase
{
	
	public function testHappyPath(): void
	{
		$elasticSearchDriver = Mockery::mock(IElasticSearchDriver::class);
		$elasticSearchDriver->shouldReceive('findById') // TODO: add arguments
			->times(1)
			->andReturn([]); // not found by ElasticSearch TODO: test found
		
		$mysqlDriver = Mockery::mock(IMySQLDriver::class);
		$mysqlDriver->shouldReceive('findProduct') // TODO: add arguments
			->times(1)
			->andReturn(['name' => 'test']); // found by MySQL - always
		
		$cacheStorage = Mockery::mock(ICacheStorage::class);
		$cacheStorage->shouldReceive('findProduct') // TODO: add arguments
			->times(1)
			->andReturn([]); // not found in cache
		$cacheStorage->shouldReceive('saveProduct') // TODO: add arguments
			->times(1)
			->andReturn([]); // saved to cache
		
		$productStatisticsStorage = Mockery::mock(IProductStatisticsStorage::class);
		$productStatisticsStorage->shouldReceive('findProduct') // TODO: add arguments
			->times(1)
			->andReturn([]); // not found in cache
		
		$controller = new ProductController($elasticSearchDriver, $mysqlDriver, $cacheStorage, $productStatisticsStorage);
		
		$response = $controller->detail('1');
		
		Assert::type('string', $response, 'A string response was returned.');
		Assert::type(stdClass::class, json_decode($response), 'A valid JSON was returned.');
	}
	
}

$test = new ProductControllerTest();
$test->run();