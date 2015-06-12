<?php
namespace Librette\Doctrine\Queries;

use Doctrine;
use Kdyby\Doctrine\DqlSelection;
use Kdyby\Doctrine\NativeQueryBuilder;
use Kdyby\Doctrine\NativeQueryWrapper;
use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQuery;
use Librette\Queries\IQueryable;
use Librette\Queries\IQueryType;
use Librette\Queries\UnexpectedValueException;
use Nette\Object;

/**
 * @author David Matejka
 */
abstract class QueryObject extends Object implements IQuery, IQueryType
{

	/** @var \Doctrine\ORM\Query */
	private $lastQuery;

	/** @var \Kdyby\Doctrine\ResultSet */
	private $lastResult;


	/**
	 * @param IQueryable
	 * @return mixed
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


	abstract protected function createQuery(Queryable $queryable);


	/**
	 * @param IQueryable
	 * @return \Doctrine\ORM\Query
	 * @internal
	 */
	public function getQuery(Queryable $repository)
	{
		$query = $this->toQuery($this->createQuery($repository));

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
	 * @return string
	 */
	public function getQueryType()
	{
		return 'doctrine';
	}


	protected function createResultSet(Doctrine\ORM\Query $query, Queryable $queryable)
	{
		return new ResultSet($queryable, $this, $queryable);
	}


	private function toQuery($query)
	{
		if ($query instanceof Doctrine\ORM\QueryBuilder) {
			$query = $query->getQuery();

		} elseif ($query instanceof DqlSelection) {
			$query = $query->createQuery();

		} elseif ($query instanceof Doctrine\ORM\NativeQuery) {
			$query = new NativeQueryWrapper($query);

		} elseif ($query instanceof NativeQueryBuilder) {
			$query = $query->getQuery();
		}

		if (!$query instanceof Doctrine\ORM\AbstractQuery) {
			throw new UnexpectedValueException(
				"Method " . $this->getReflection()->getMethod('doCreateQuery') . " must return " .
				"instanceof Doctrine\\ORM\\Query or Kdyby\\Doctrine\\QueryBuilder or Kdyby\\Doctrine\\DqlSelection, " .
				(is_object($query) ? 'instance of ' . get_class($query) : gettype($query)) . " given."
			);
		}

		return $query;
	}


}
