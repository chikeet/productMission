<?php
/**
 * @testCase
 * @phpVersion >= 7.1
 */

require __DIR__ . '/../../../../bootstrap.php';
require __DIR__ . '/../../../../../src/ProductMission/Controllers/ProductController.php';

use Tester\Assert;
use Tester\TestCase;
use ProductMission\Caching\ICacheStorage;
use ProductMission\Drivers\IElasticSearchDriver;
use ProductMission\Drivers\IMySQLDriver;
use ProductMission\Statistics\IProductStatisticsStorage;
use ProductMission\Controllers\ProductController;

class ProductControllerTest extends TestCase
{
	
	/* Tests ******************************************************************/
	
	public function testHappyPath(): void
	{
		/* Setup mocks */
		$cacheStorage = Mockery::mock(ICacheStorage::class);
		$cacheStorage->shouldReceive('findProduct')
			->withArgs(['123'])
			->times(1)
			->andReturn(['name' => 'Test product']); // found in cache
		
		$elasticSearchDriver = Mockery::mock(IElasticSearchDriver::class);
		
		$mysqlDriver = Mockery::mock(IMySQLDriver::class);
		
		$productStatisticsStorage = $this->getStatisticStorageMock('123');
		
		/* Create controller and get response */
		$controller = new ProductController($elasticSearchDriver, $mysqlDriver, $cacheStorage, $productStatisticsStorage);
		$response = $controller->detail('123');
		
		/* Asserts */
		Assert::type('string', $response, 'A string response was returned.');
		Assert::type(stdClass::class, json_decode($response), 'A valid JSON was returned.');
	}
	
	
	public function testNotFoundInCache(): void
	{
		$cacheStorage = $this->getCacheNotFoundMock('123', ['name' => 'Test product']);
		
		$elasticSearchDriver = Mockery::mock(IElasticSearchDriver::class);
		$elasticSearchDriver->shouldReceive('findById')
			->withArgs(['123'])
			->times(1)
			->andReturn(['name' => 'Test product']); // found by ElasticSearch
		
		$mysqlDriver = Mockery::mock(IMySQLDriver::class);
		
		$productStatisticsStorage = $this->getStatisticStorageMock('123');
		
		$controller = new ProductController($elasticSearchDriver, $mysqlDriver, $cacheStorage, $productStatisticsStorage);
		$response = $controller->detail('123');
		
		/* Asserts */
		Assert::type('string', $response, 'A string response was returned.');
		Assert::type(stdClass::class, json_decode($response), 'A valid JSON was returned.');
	}
	
	
	public function testNotFoundByElastic(): void
	{
		$cacheStorage = $this->getCacheNotFoundMock('123', ['name' => 'Test product']);
		
		$elasticSearchDriver = Mockery::mock(IElasticSearchDriver::class);
		$elasticSearchDriver->shouldReceive('findById')
			->withArgs(['123'])
			->times(1)
			->andReturn([]); // not found by ElasticSearch
		
		$mysqlDriver = Mockery::mock(IMySQLDriver::class);
		$mysqlDriver->shouldReceive('findProduct')
			->withArgs(['123'])
			->times(1)
			->andReturn(['name' => 'Test product']); // found by MySQL - always
		
		$productStatisticsStorage = $this->getStatisticStorageMock('123');
		
		$controller = new ProductController($elasticSearchDriver, $mysqlDriver, $cacheStorage, $productStatisticsStorage);
		$response = $controller->detail('123');
		
		/* Asserts */
		Assert::type('string', $response, 'A string response was returned.');
		Assert::type(stdClass::class, json_decode($response), 'A valid JSON was returned.');
	}
	
	/* Setup and teardown *****************************************************/
	
	public function tearDown()
	{
		Mockery::close();
		Tester\Environment::$checkAssertions = FALSE;
		
		parent::tearDown();
	}
	
	
	/* Mock factories *********************************************************/
	
	/**
	 * @param string $productId
	 * @return \Mockery\MockInterface
	 */
	protected function getStatisticStorageMock(string $productId): \Mockery\MockInterface
	{
		$productStatisticsStorage = Mockery::mock(IProductStatisticsStorage::class);
		$productStatisticsStorage->shouldReceive('increaseProductSearchCount')
			->withArgs([$productId])
			->times(1); // not found in cache
		
		return $productStatisticsStorage;
	}
	
	
	/**
	 * @param string $productId
	 * @param array $productData
	 * @return \Mockery\MockInterface
	 */
	protected function getCacheNotFoundMock(string $productId, array $productData): \Mockery\MockInterface
	{
		$cacheStorage = Mockery::mock(ICacheStorage::class);
		$cacheStorage->shouldReceive('findProduct')
			->withArgs([$productId])
			->times(1)
			->andReturn([]); // not found in cache
		$cacheStorage->shouldReceive('saveProduct')
			->withArgs([$productId, $productData])
			->times(1); // saved to cache
		
		return $cacheStorage;
	}
	
}

$test = new ProductControllerTest();
$test->run();