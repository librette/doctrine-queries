<?php
namespace Librette\Doctrine\Queries;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryBuilder;
use Librette\Queries\IQueryable;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IQueryHandlerAccessor;
use Nette\SmartObject;

/**
 * @author David Matejka
 */
class Queryable implements IQueryable
{
	use SmartObject;

	/** @var EntityManager */
	protected $entityManager;

	/** @var IQueryHandlerAccessor */
	private $queryHandlerAccessor;


	/**
	 * @param EntityManager
	 * @param IQueryHandlerAccessor
	 */
	public function __construct(EntityManager $entityManager, IQueryHandlerAccessor $queryHandlerAccessor)
	{
		$this->entityManager = $entityManager;
		$this->queryHandlerAccessor = $queryHandlerAccessor;
	}


	/**
	 * @param string|null
	 * @param string|null
	 * @param string|null
	 * @return QueryBuilder
	 */
	public function createQueryBuilder($entityClass = NULL, $alias = NULL, $indexBy = NULL)
	{
		$qb = $this->entityManager->createQueryBuilder();
		if ($entityClass) {
			$qb->from($entityClass, $alias, $indexBy);
			$qb->select($alias);
		}

		return $qb;
	}


	public function createQuery($query)
	{
		return $this->entityManager->createQuery($query);
	}


	/**
	 * @return IQueryHandler
	 */
	public function getHandler()
	{
		return $this->queryHandlerAccessor->get();
	}


	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

}
