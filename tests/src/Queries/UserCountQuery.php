<?php
namespace LibretteTests\Doctrine\Queries\Queries;

use Librette\Doctrine\Queries\BaseQueryObject;
use Librette\Doctrine\Queries\Queryable;
use LibretteTests\Doctrine\Queries\Model\User;

/**
 * @author David Matejka
 */
class UserCountQuery extends BaseQueryObject
{


	protected function doFetch(Queryable $queryable)
	{
		return (int) $queryable->createQueryBuilder(User::class, 'u')
			->select('COUNT(u.id) AS c')
			->getQuery()->getSingleScalarResult();
	}

}
