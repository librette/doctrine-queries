<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

/**
 * @author David Matejka
 */
class PairsQuery extends BaseQueryObject
{

	/** @var string */
	private $value;

	/** @var array */
	private $filter = [];

	/** @var array */
	private $orderBy = [];

	/** @var string */
	private $key;

	/** @var string */
	private $entityName;


	public function __construct(string $entityName, string $value)
	{
		$this->value = $value;
		$this->entityName = $entityName;
	}


	public function setFilter(array $filter): self
	{
		$this->filter = $filter;

		return $this;
	}


	public function setKey(string $key): self
	{
		$this->key = $key;

		return $this;
	}


	public function setOrderBy(array $orderBy): self
	{
		$this->orderBy = $orderBy;

		return $this;
	}


	protected function doFetch(Queryable $queryable)
	{
		return $queryable->getEntityManager()
			->getRepository($this->entityName)
			->findPairs($this->filter, $this->value, $this->orderBy, $this->key);
	}
}
