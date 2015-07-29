<?php
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


	public function __construct($entityName, $value)
	{
		$this->value = $value;
		$this->entityName = $entityName;
	}


	/**
	 * @param array
	 * @return self
	 */
	public function setFilter(array $filter)
	{
		$this->filter = $filter;

		return $this;
	}


	/**
	 * @param string
	 * @return self
	 */
	public function setKey($key)
	{
		$this->key = $key;

		return $this;
	}


	/**
	 * @param array
	 * @return self
	 */
	public function setOrderBy(array $orderBy)
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
