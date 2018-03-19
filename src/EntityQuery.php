<?php declare(strict_types = 1);

namespace Librette\Doctrine\Queries;

class EntityQuery extends BaseQueryObject
{

	/** @var string */
	private $entityName;

	/** @var int|mixed */
	private $id;


	/**
	 * @param int|mixed $id
	 */
	public function __construct(string $entityName, $id)
	{
		$this->entityName = $entityName;
		$this->id = $id;
	}


	protected function doFetch(Queryable $queryable)
	{
		return $queryable->getEntityManager()->find($this->entityName, $this->id);
	}

}
