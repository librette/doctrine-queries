<?php
namespace Librette\Doctrine\Queries;

use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQueryable;

/**
 * @author David Matejka
 */
trait TQueryObject
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
