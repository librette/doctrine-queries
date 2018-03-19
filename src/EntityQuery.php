<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

/**
 * @author David Matejka
 */
class EntityQuery extends BaseQueryObject
{

	/** @var */
	private $entityName;

	/** @var */
	private $id;


	/**
	 * @param string
	 * @param int|mixed
	 */
	public function __construct($entityName, $id)
	{
		$this->entityName = $entityName;
		$this->id = $id;
	}


	protected function doFetch(Queryable $queryable)
	{
		return $queryable->getEntityManager()->find($this->entityName, $this->id);
	}

}
