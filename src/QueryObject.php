<?php
namespace Librette\Doctrine\Queries;

use Doctrine;
use Doctrine\ORM\QueryBuilder;
use Librette\Doctrine\Queries\Specifications\TSpecificationQuery;
use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQueryable;
use Librette\Queries\IResultSet;
use Librette\Queries\IResultSetQuery;
use Nette\SmartObject;

/**
 * @author David Matejka
 *
 * @method onPostFetch(QueryObject $self, Queryable $queryable, \Traversable $data)
 */
abstract class QueryObject implements IResultSetQuery, IQuery
{
	use SmartObject;
	use TSpecificationQuery;

	/** @var callable[] */
	public $onPostFetch = [];

	/** @var \Doctrine\ORM\Query */
	private $lastQuery;

	/** @var IResultSet */
	private $lastResult;


	/**
	 * @param IQueryable
	 * @return IResultSet
	 */
	public function fetch(IQueryable $queryable)
	{
		if (!$queryable instanceof Queryable) {
			throw new InvalidArgumentException("\$queryable must be an instance of " . Queryable::class);
		}
		$this->getQuery($queryable)
			->setFirstResult(NULL)
			->setMaxResults(NULL);

		return $this->lastResult;
	}


	/**
	 * @param IQueryable
	 * @return \Doctrine\ORM\Query
	 * @internal
	 */
	public function getQuery(Queryable $repository)
	{
		$qb = $this->createQuery($repository);
		$this->applySpecifications($qb, $qb->getRootAliases()[0]);

		$query = $qb->getQuery();
		$this->modifyQuery($query);

		if ($this->lastQuery && $this->lastQuery->getDQL() === $query->getDQL()) {
			$query = $this->lastQuery;
		}

		if ($this->lastQuery !== $query) {
			$this->lastResult = $this->createResultSet($query, $repository);
		}

		return $this->lastQuery = $query;
	}


	/**
	 * @internal
	 * @return \Doctrine\ORM\Query
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}


	/**
	 * @param Doctrine\ORM\Query
	 * @param Queryable
	 * @return IResultSet
	 */
	protected function createResultSet(Doctrine\ORM\Query $query, Queryable $queryable)
	{
		return new ResultSet($query, $this, $queryable);
	}


	/**
	 * @param Queryable
	 * @return QueryBuilder
	 */
	abstract protected function createQuery(Queryable $queryable);


	/**
	 * @param Queryable
	 * @param \Traversable
	 * @internal
	 */
	public function queryFetched(Queryable $queryable, \Traversable $data)
	{
		$this->onPostFetch($this, $queryable, $data);
	}

}
