<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

class SelectOneQuery extends BaseQueryObject
{

	/** @var string */
	private $entityClass;

	/** @var array */
	private $filters = [];

	/** @var array */
	private $orderBy = [];


	public function __construct(string $entityClass, array $filters = [])
	{
		$this->entityClass = $entityClass;
		$this->filters = array_map(NULL, array_keys($filters), $filters);
	}


	/**
	 * @param string|\Closure
	 * @param string|array|null|mixed
	 */
	public function filterBy($field, $value = NULL): self
	{
		$this->filters[] = [$field, $value];

		return $this;
	}


	/**
	 * @param string
	 * @param string
	 */
	public function orderBy($field, $direction = 'ASC'): self
	{
		$this->orderBy[$field] = $direction;

		return $this;
	}


	protected function doFetch(Queryable $queryable)
	{
		$qb = $queryable->createQueryBuilder($this->entityClass, 'e');
		foreach ($this->filters as $filter) {
			[$field, $value] = $filter;
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
