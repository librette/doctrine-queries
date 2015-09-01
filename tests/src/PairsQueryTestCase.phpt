<?php
namespace LibretteTests\Doctrine\Queries;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Librette\Doctrine\Queries\PairsQuery;
use Librette\Doctrine\Queries\Queryable;
use Librette\Doctrine\Queries\QueryHandler;
use Librette\Queries\IQueryHandlerAccessor;
use LibretteTests\Doctrine\Queries\Model\User;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author David Matějka
 * @testCase
 */
class PairsQueryTestCase extends Tester\TestCase
{
	public function tearDown()
	{
		\Mockery::close();
	}


	public function testBasic()
	{
		$repo = \Mockery::mock(EntityRepository::class)
			->shouldReceive('findPairs')
			->once()
			->withArgs([
				['name' => 'John'],
				'name',
				['name' => 'ASC'],
				'id'
			])
			->getMock();
		$em = \Mockery::mock(EntityManager::class)
			->shouldReceive('getRepository')
			->andReturn($repo)
			->getMock();
		$queryHandler = new QueryHandler(new Queryable($em, \Mockery::mock(IQueryHandlerAccessor::class)));
		$query = new PairsQuery(User::class, 'name');
		$query->setKey('id');
		$query->setFilter(['name' => 'John']);
		$query->setOrderBy(['name' => 'ASC']);
		$queryHandler->fetch($query);

		Assert::true(TRUE);
	}

}


\run(new PairsQueryTestCase());
