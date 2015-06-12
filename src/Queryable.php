<?php
namespace Librette\Doctrine\Queries;

use Doctrine\ORM\EntityManager;
use Librette\Queries\IQueryable;
use Librette\Queries\IQueryHandler;
use Nette\Object;

/**
 * @author David Matejka
 */
class Queryable extends Object implements IQueryable
{

	/** @var EntityManager */
	protected $entityManager;


	/**
	 * @param EntityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function createQueryBuilder($entityClass = NULL, $alias = NULL)
	{
		$qb = $this->entityManager->createQueryBuilder();
		if ($entityClass) {
			$qb->from($entityClass, $alias);
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
		return NULL; //todo
	}


}
