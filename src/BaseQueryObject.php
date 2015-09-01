<?php
namespace Librette\Doctrine\Queries;

use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQueryable;
use Nette\Object;

/**
 * @author David Matejka
 */
abstract class BaseQueryObject extends Object implements IQuery
{

	/**
	 * @param IQueryable
	 * @return mixed
	 */
	public function fetch(IQueryable $queryable)
	{
		if (!$queryable instanceof Queryable) {
			throw new InvalidArgumentException("\$queryable must be an instance of " . Queryable::class);
		}

		return $this->doFetch($queryable);
	}


	/**
	 * @param Queryable
	 * @return mixed result
	 */
	abstract protected function doFetch(Queryable $queryable);

}
