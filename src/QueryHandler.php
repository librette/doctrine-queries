<?php
namespace Librette\Doctrine\Queries;

use Librette\Queries\IQuery;
use Librette\Queries\IQueryHandler;
use Librette\Queries\IResultSet;
use Nette\Object;

/**
 * @author David Matejka
 */
class QueryHandler extends Object implements IQueryHandler
{

	/** @var Queryable */
	protected $queryable;


	/**
	 * @param Queryable
	 */
	public function __construct(Queryable $queryable)
	{
		$this->queryable = $queryable;
	}


	/**
	 * @param IQuery
	 * @return mixed|IResultSet
	 */
	public function handle(IQuery $query)
	{
		return $query->fetch($this->queryable);
	}

}
