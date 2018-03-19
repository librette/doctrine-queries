<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

use Librette\Queries\IQuery as BaseQuery;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IResultSet;
use Nette\SmartObject;

/**
 * @author David Matejka
 */
class QueryHandler implements IQueryHandler
{
	use SmartObject;

	/** @var Queryable */
	protected $queryable;


	public function __construct(Queryable $queryable)
	{
		$this->queryable = $queryable;
	}


	public function supports(BaseQuery $query): bool
	{
		return $query instanceof IQuery;
	}


	/**
	 * @param IQuery
	 * @return mixed|IResultSet
	 */
	public function fetch(BaseQuery $query)
	{
		return $query->fetch($this->queryable);
	}

}
