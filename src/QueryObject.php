<?php declare(strict_types = 1);

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


	public function fetch(IQueryable $queryable): IResultSet
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
	 * @internal
	 */
	public function getQuery(Queryable $repository): Doctrine\ORM\Query
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
	 */
	public function getLastQuery(): ?Doctrine\ORM\Query
	{
		return $this->lastQuery;
	}


	protected function createResultSet(Doctrine\ORM\Query $query, Queryable $queryable): IResultSet
	{
		return new ResultSet($query, $this, $queryable);
	}


	abstract protected function createQuery(Queryable $queryable): QueryBuilder;


	/**
	 * @internal
	 */
	public function queryFetched(Queryable $queryable, \Traversable $data)
	{
		$this->onPostFetch($this, $queryable, $data);
	}
}
