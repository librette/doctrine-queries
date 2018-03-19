<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryBuilder;
use Librette\Queries\IQueryable;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IQueryHandlerAccessor;
use Nette\SmartObject;

class Queryable implements IQueryable
{
	use SmartObject;

	/** @var EntityManager */
	protected $entityManager;

	/** @var IQueryHandlerAccessor */
	private $queryHandlerAccessor;


	public function __construct(EntityManager $entityManager, IQueryHandlerAccessor $queryHandlerAccessor)
	{
		$this->entityManager = $entityManager;
		$this->queryHandlerAccessor = $queryHandlerAccessor;
	}


	public function createQueryBuilder(?string $entityClass = NULL, ?string $alias = NULL, ?string $indexBy = NULL): QueryBuilder
	{
		$qb = $this->entityManager->createQueryBuilder();
		if ($entityClass) {
			$qb->from($entityClass, $alias, $indexBy);
			$qb->select($alias);
		}

		return $qb;
	}


	public function createQuery(string $query): Query
	{
		return $this->entityManager->createQuery($query);
	}


	public function getHandler(): IQueryHandler
	{
		return $this->queryHandlerAccessor->get();
	}


	public function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}
}
