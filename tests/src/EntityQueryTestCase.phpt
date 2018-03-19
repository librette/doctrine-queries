<?php
namespace LibretteTests\Doctrine\Queries;

use Doctrine\DBAL\Logging\DebugStack;
use Librette\Doctrine\Queries\EntityQuery;
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
class EntityQueryTestCase extends Tester\TestCase
{
	use EntityManagerTest;


	public function setUp()
	{
	}


	public function testAfterInsert()
	{
		$em = $this->createMemoryManager();
		$queryHandler = new QueryHandler(new Queryable($em, \Mockery::mock(IQueryHandlerAccessor::class)));
		$em->persist($user = new User('John'))->flush();
		$em->getConnection()->getConfiguration()->setSQLLogger($logger = new DebugStack());
		Assert::same(0, $logger->currentQuery);
		$query = new EntityQuery(User::class, $user->getId());
		Assert::same($user, $queryHandler->fetch($query));
		Assert::same(0, $logger->currentQuery);
	}


	public function testRepeatedSelect()
	{
		$em = $this->createMemoryManager();
		$queryHandler = new QueryHandler(new Queryable($em, \Mockery::mock(IQueryHandlerAccessor::class)));
		$em->persist($user = new User('John'))->flush();
		$em->clear();

		$em->getConnection()->getConfiguration()->setSQLLogger($logger = new DebugStack());
		Assert::same(0, $logger->currentQuery);
		$query = new EntityQuery(User::class, $user->getId());
		Assert::same($user->getId(), $user2 = $queryHandler->fetch($query)->getId());
		Assert::same(1, $logger->currentQuery);

		$query = new EntityQuery(User::class, $user->getId());
		Assert::same($user2, $queryHandler->fetch($query)->getId());
		Assert::same(1, $logger->currentQuery);
	}

}


\run(new EntityQueryTestCase());
