<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

use Librette\Queries\InvalidArgumentException;
use Librette\Queries\IQueryable;
use Nette\SmartObject;

/**
 * @author David Matejka
 */
abstract class BaseQueryObject implements IQuery
{
	use SmartObject;


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
