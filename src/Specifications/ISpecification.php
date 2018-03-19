<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries\Specifications;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface ISpecification
{
	/**
	 * @return void|string|array|mixed
	 */
	public function match(QueryBuilder $queryBuilder, string $alias);

	public function modifyQuery(Query $query): void;
}
