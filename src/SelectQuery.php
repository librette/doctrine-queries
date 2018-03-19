<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

use Doctrine\ORM\QueryBuilder;

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


	public function __construct(string $entityClass)
	{
		$this->entityClass = $entityClass;
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


	public function orderBy(string $field, string $direction = 'ASC'): self
	{
		$this->orderBy[$field] = $direction;

		return $this;
	}


	public function indexBy(string $field): self
	{
		if (strpos($field, '.') === FALSE) {
			$field = 'e.' . $field;
		}
		$this->indexBy = $field;

		return $this;
	}


	protected function createQuery(Queryable $queryable): QueryBuilder
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

		return $qb;
	}

}
