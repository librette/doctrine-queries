<?php
namespace Librette\Doctrine\Queries;

use Doctrine\ORM;
use Doctrine\ORM\Tools\Pagination\Paginator as ResultPaginator;
use Kdyby\Doctrine\NativeQueryWrapper;
use Librette\Queries\InvalidStateException;
use Librette\Queries\IResultSet;
use Nette;
use Nette\Utils\Paginator as UIPaginator;


/**
 * @author David Matejka
 */
class ResultSet extends Nette\Object implements \IteratorAggregate, IResultSet
{

	/** @var int */
	private $totalCount;

	/** @var \Doctrine\ORM\Query */
	private $query;

	/** @var QueryObject */
	private $queryObject;

	/** @var Queryable */
	private $repository;

	/** @var bool */
	private $fetchJoinCollection = TRUE;

	/** @var bool|null */
	private $useOutputWalkers;

	/** @var \Iterator */
	private $iterator;

	/** @var bool */
	private $frozen = FALSE;


	/**
	 * @param \Doctrine\ORM\AbstractQuery
	 * @param QueryObject
	 * @param Queryable
	 */
	public function __construct(ORM\AbstractQuery $query, QueryObject $queryObject = NULL, Queryable $repository = NULL)
	{
		$this->query = $query;
		$this->queryObject = $queryObject;
		$this->repository = $repository;
		if ($this->query instanceof NativeQueryWrapper || $this->query instanceof ORM\NativeQuery) {
			$this->fetchJoinCollection = FALSE;
		}
	}


	/**
	 * @param bool
	 * @throws InvalidStateException
	 * @return ResultSet
	 */
	public function setFetchJoinCollection($fetchJoinCollection)
	{
		$this->updating();

		$this->fetchJoinCollection = (bool) $fetchJoinCollection;
		$this->iterator = NULL;

		return $this;
	}


	/**
	 * @param bool|null
	 * @throws InvalidStateException
	 * @return ResultSet
	 */
	public function setUseOutputWalkers($useOutputWalkers)
	{
		$this->updating();

		$this->useOutputWalkers = $useOutputWalkers;
		$this->iterator = NULL;

		return $this;
	}


	/**
	 * @return bool|null
	 */
	public function getUseOutputWalkers()
	{
		return $this->useOutputWalkers;
	}


	/**
	 * @return boolean
	 */
	public function getFetchJoinCollection()
	{
		return $this->fetchJoinCollection;
	}


	/**
	 * @param int
	 * @param int
	 * @return ResultSet
	 */
	public function applyPaging($offset, $limit)
	{
		if ($this->query->getFirstResult() != $offset || $this->query->getMaxResults() != $limit) {
			$this->query->setFirstResult($offset);
			$this->query->setMaxResults($limit);
			$this->iterator = NULL;
		}

		return $this;
	}


	/**
	 * @param \Nette\Utils\Paginator
	 * @param int
	 * @return ResultSet
	 */
	public function applyPaginator(UIPaginator $paginator, $itemsPerPage = NULL)
	{
		if ($itemsPerPage !== NULL) {
			$paginator->setItemsPerPage($itemsPerPage);
		}

		$paginator->setItemCount($this->getTotalCount());
		$this->applyPaging($paginator->getOffset(), $paginator->getLength());

		return $this;
	}


	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		$count = $this->getTotalCount();
		$offset = $this->query->getFirstResult();

		return $count <= $offset;
	}


	/**
	 * @throws \Kdyby\Doctrine\QueryException
	 * @return int
	 */
	public function getTotalCount()
	{
		if ($this->totalCount === NULL) {
			$this->frozen = TRUE;
			$paginatedQuery = $this->createPaginatedQuery($this->query);
			$this->totalCount = $paginatedQuery->count();
		}

		return $this->totalCount;
	}


	/**
	 * @param int $hydrationMode
	 * @return \ArrayIterator
	 */
	public function getIterator($hydrationMode = ORM\AbstractQuery::HYDRATE_OBJECT)
	{
		if ($this->iterator !== NULL) {
			return $this->iterator;
		}

		$this->query->setHydrationMode($hydrationMode);
		$this->frozen = TRUE;
		if ($this->fetchJoinCollection && ($this->query->getMaxResults() > 0 || $this->query->getFirstResult() > 0)) {
			$this->iterator = $this->createPaginatedQuery($this->query)->getIterator();
		} else {
			$this->iterator = new \ArrayIterator($this->query->getResult(NULL));
		}


		return $this->iterator;

	}


	/**
	 * @param int
	 * @return array
	 */
	public function toArray($hydrationMode = ORM\AbstractQuery::HYDRATE_OBJECT)
	{
		return iterator_to_array(clone $this->getIterator($hydrationMode), TRUE);
	}


	/**
	 * @return int
	 */
	public function count()
	{
		return $this->getIterator()->count();
	}


	/**
	 * @param ORM\Query $query
	 * @return ResultPaginator
	 */
	private function createPaginatedQuery(ORM\Query $query)
	{
		$paginated = new ResultPaginator($query, $this->fetchJoinCollection);
		$paginated->setUseOutputWalkers($this->useOutputWalkers);

		return $paginated;
	}


	private function updating()
	{
		if ($this->frozen !== FALSE) {
			throw new InvalidStateException("Cannot modify result set, that was already fetched from storage.");
		}
	}

}
