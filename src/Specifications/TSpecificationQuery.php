<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @author David Matejka
 */
trait TSpecificationQuery
{

	/** @var ISpecification[] */
	private $specifications = [];


	public function addSpecification(ISpecification $specification)
	{
		$this->specifications[] = $specification;

		return $this;
	}


	protected function applySpecifications(QueryBuilder $queryBuilder, $alias)
	{
		$andX = new Query\Expr\Andx();
		foreach ($this->specifications as $specification) {
			array_map([$andX, 'add'], array_filter((array) $specification->match($queryBuilder, $alias)));
		}
		if ($andX->count() > 0) {
			$queryBuilder->andWhere($andX);
		}
	}


	protected function modifyQuery(Query $query)
	{
		foreach ($this->specifications as $specification) {
			$specification->modifyQuery($query);
		}
	}

}
