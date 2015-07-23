<?php
namespace Librette\Doctrine\Queries;

use Doctrine\ORM\EntityManager;
use Librette\Queries\IQueryable;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IQueryHandlerAccessor;
use Nette\Object;

/**
 * @author David Matejka
 */
class Queryable extends Object implements IQueryable
{

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
