<?php
namespace LibretteTests\Doctrine\Queries\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\SmartObject;

/**
 * @ORM\Entity
 */
class User
{
	use SmartObject;
	use Identifier;


	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $name;


	public function __construct($name)
	{
		$this->name = $name;
	}


}
