<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

use Doctrine\ORM;
use Doctrine\ORM\Tools\Pagination\Paginator as ResultPaginator;
use Kdyby\Doctrine\NativeQueryWrapper;
use Librette\Queries\InvalidStateException;
use Librette\Queries\IResultSet;
use Nette;
use Nette\Utils\Paginator as UIPaginator;


class ResultSet implements \IteratorAggregate, IResultSet
{
	use Nette\SmartObject;

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
	public function setFetchJoinCollection($fetchJoinCollection): self
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
	public function setUseOutputWalkers($useOutputWalkers): self
	{
		$this->updating();

		$this->useOutputWalkers = $useOutputWalkers;
		$this->iterator = NULL;

		return $this;
	}


	/**
	 * @return self
	 */
	public function applyPaging(int $offset, int $limit): IResultSet
	{
		if ($this->query->getFirstResult() != $offset || $this->query->getMaxResults() != $limit) {
			$this->query->setFirstResult($offset);
			$this->query->setMaxResults($limit);
			$this->iterator = NULL;
		}

		return $this;
	}


	/**
	 * @return self
	 */
	public function applyPaginator(UIPaginator $paginator, ?int $itemsPerPage = NULL): IResultSet
	{
		if ($itemsPerPage !== NULL) {
			$paginator->setItemsPerPage($itemsPerPage);
		}

		$paginator->setItemCount($this->getTotalCount());
		$this->applyPaging($paginator->getOffset(), $paginator->getLength());

		return $this;
	}


	public function isEmpty(): bool
	{
		$count = $this->getTotalCount();
		$offset = $this->query->getFirstResult();

		return $count <= $offset;
	}


	/**
	 * @throws \Kdyby\Doctrine\QueryException
	 * @return int
	 */
	public function getTotalCount(): int
	{
		if ($this->totalCount === NULL) {
			$this->frozen = TRUE;
			$paginatedQuery = $this->createPaginatedQuery($this->query);
			$this->totalCount = $paginatedQuery->count();
		}

		return $this->totalCount;
	}


	public function getIterator(?int $hydrationMode = NULL): \Iterator
	{
		if ($this->iterator !== NULL) {
			return $this->iterator;
		}
		if ($hydrationMode !== NULL) {
			$this->query->setHydrationMode($hydrationMode);
		}
		$this->frozen = TRUE;
		if ($this->fetchJoinCollection && ($this->query->getMaxResults() > 0 || $this->query->getFirstResult() > 0)) {
			$this->iterator = $this->createPaginatedQuery($this->query)->getIterator();
		} else {
			$this->iterator = new \ArrayIterator($this->query->getResult(NULL));
		}
		if ($this->repository && $this->queryObject) {
			$this->queryObject->queryFetched($this->repository, $this->iterator);
		}

		return $this->iterator;
	}


	public function toArray(?int $hydrationMode = NULL): array
	{
		return iterator_to_array(clone $this->getIterator($hydrationMode), TRUE);
	}


	public function count(): int
	{
		return $this->getIterator()->count();
	}


	private function createPaginatedQuery(ORM\Query $query): ResultPaginator
	{
		$paginated = new ResultPaginator($query, $this->fetchJoinCollection);
		$paginated->setUseOutputWalkers($this->useOutputWalkers);

		return $paginated;
	}


	private function updating(): void
	{
		if ($this->frozen !== FALSE) {
			throw new InvalidStateException("Cannot modify result set, that was already fetched from storage.");
		}
	}
}
