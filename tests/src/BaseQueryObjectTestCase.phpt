<?php
namespace LibretteTests\Doctrine\Queries;

use Librette\Doctrine\Queries\Queryable;
use Librette\Doctrine\Queries\QueryHandler;
use Librette\Queries\Internal\InternalQueryable;
use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IQueryHandlerAccessor;
use Librette\Queries\MainQueryHandler;
use LibretteTests\Doctrine\Queries\Model\User;
use LibretteTests\Doctrine\Queries\Queries\UserCountQuery;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author David Matějka
 * @testCase
 */
class BaseQueryObjectTestCase extends Tester\TestCase
{
	use EntityManagerTest;


	public function tearDown()
	{
		\Mockery::close();
	}


	public function testBasic()
	{
		$em = $this->createMemoryManager();
		$queryHandler = new MainQueryHandler();
		$accessor = \Mockery::mock(IQueryHandlerAccessor::class)->shouldReceive('get')->andReturn($queryHandler)->getMock();
		$queryHandler->addHandler(new QueryHandler(new Queryable($em, $accessor)));
		$em->persist(new User('John'))->persist(new User('Jack'))->flush();
		Assert::same(2, $queryHandler->fetch(new UserCountQuery()));
	}


	public function testInvalidQueryable()
	{
		Assert::throws(function () {
			(new UserCountQuery())->fetch(new InternalQueryable(\Mockery::mock(IQueryHandler::class)));
		}, InvalidArgumentException::class);
	}

}


\run(new BaseQueryObjectTestCase());
