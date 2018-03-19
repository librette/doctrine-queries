<?php
namespace Librette\Doctrine\Queries;

use Librette\Doctrine\Queries\DI\DoctrineQueriesExtension;
use Librette\Queries\DI\QueriesExtension;
use Librette\Queries\IQueryHandler;
use LibretteTests\Doctrine\Queries\Mocks\EntityManagerMock;
use LibretteTests\Doctrine\Queries\Queries\UserCountQuery;
use Nette;
use Nette\DI\Compiler;
use Nette\DI\ContainerLoader;
use Tester;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class ExtensionTestCase extends Tester\TestCase
{

	public function setUp()
	{
	}


	public function testConfig()
	{
		$loader = new ContainerLoader(TEMP_DIR);
		$cls = $loader->load(function (Compiler $compiler) {
			$compiler->getContainerBuilder()->addDefinition('em')
				->setClass(EntityManagerMock::class);
			$compiler->addExtension('doctrineQueries', new DoctrineQueriesExtension());
			$compiler->addExtension('queries', new QueriesExtension());
		});
		/** @var Nette\DI\Container $container */
		$container = new $cls;
		/** @var IQueryHandler $queryHandler */
		$queryHandler = $container->getByType(IQueryHandler::class);

		Tester\Assert::true($queryHandler->supports(new UserCountQuery()));
	}

}


\run(new ExtensionTestCase());
