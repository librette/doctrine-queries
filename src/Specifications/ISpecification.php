<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @author David Matejka
 */
interface ISpecification
{

	/**
	 * @param QueryBuilder
	 * @param string
	 * @return void|string|array|mixed
	 */
	public function match(QueryBuilder $queryBuilder, $alias);


	/**
	 * @param Query
	 * @return void
	 */
	public function modifyQuery(Query $query);

}
