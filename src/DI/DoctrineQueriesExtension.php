<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries\DI;

use Librette\Doctrine\Queries\Queryable;
use Librette\Doctrine\Queries\QueryHandler;
use Librette\Queries\DI\QueriesExtension;
use Nette\DI\CompilerExtension;

/**
 * @author David Matejka
 */
class DoctrineQueriesExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('queryable'))
			->setClass(Queryable::class);
		$builder->addDefinition($this->prefix('queryHandler'))
			->setClass(QueryHandler::class)
			->addTag(QueriesExtension::TAG_QUERY_HANDLER)
			->setAutowired(FALSE);
	}

}
