<?php
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
		foreach ($this->specifications as $specification) {
			$specification->match($queryBuilder, $alias);
		}
	}


	protected function modifyQuery(Query $query)
	{
		foreach ($this->specifications as $specification) {
			$specification->modifyQuery($query);
		}
	}

}
