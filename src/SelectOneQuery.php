<?php
namespace Librette\Doctrine\Queries;

/**
 * @author David Matejka
 */
class SelectOneQuery extends BaseQueryObject
{

	/** @var string */
	private $entityClass;

	/** @var array */
	private $filters = [];

	/** @var array */
	private $orderBy = [];

	/**
	 * @param string
	 */
	public function __construct($entityClass, array $filters = [])
	{
		$this->entityClass = $entityClass;
		$this->filters = array_map(NULL, array_keys($filters), $filters);
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


	protected function doFetch(Queryable $queryable)
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
		$qb->setMaxResults(1);

		return $qb->getQuery()->getOneOrNullResult();
	}

}
