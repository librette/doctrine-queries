<?php
namespace Librette\Doctrine\Queries;

/**
 * @author David Matejka
 */
class SelectQuery extends QueryObject
{

	/** @var string */
	private $entityClass;

	/** @var array */
	private $filters = [];

	/** @var array */
	private $orderBy = [];

	/** @var string */
	private $indexBy;


	/**
	 * @param string
	 */
	public function __construct($entityClass)
	{
		$this->entityClass = $entityClass;
	}


	/**
	 * @param string|\Closure
	 * @param string|array|null|mixed
	 * @return self
	 */
	public function filterBy($field, $value = NULL)
	{
		$this->filters[] = [$field, $value];

		return $this;
	}


	/**
	 * @param string
	 * @param string
	 * @return self
	 */
	public function orderBy($field, $direction = 'ASC')
	{
		$this->orderBy[$field] = $direction;

		return $this;
	}


	/**
	 * @param string
	 * @return self
	 */
	public function indexBy($field)
	{
		if (strpos($field, '.') === FALSE) {
			$field = 'e.' . $field;
		}
		$this->indexBy = $field;

		return $this;
	}


	protected function createQuery(Queryable $queryable)
	{
		$qb = $queryable->createQueryBuilder($this->entityClass, 'e');
		foreach ($this->filters as $filter) {
			list ($field, $value) = $filter;
			if ($value === NULL && $field instanceof \Closure) {
				$field($qb, 'e');
			} else {
				$qb->whereCriteria([$field => $value]);
			}
		}
		foreach ($this->orderBy as $field => $direction) {
			$qb->autoJoinOrderBy($field, $direction);
		}

		return $qb;
	}

}
