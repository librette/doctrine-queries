<?php
namespace LibretteTests\Doctrine\Queries;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\ORM\Tools\SchemaTool;
use Kdyby;
use Nette;


trait EntityManagerTest
{

	/**
	 * @return Kdyby\Doctrine\EntityManager
	 */
	protected function createMemoryManager($createSchema = TRUE)
	{
		$conf = [
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		];
		$connection = new Kdyby\Doctrine\Connection($conf, new Driver());
		$config = new Kdyby\Doctrine\Configuration();
		$cache = new ArrayCache();
		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir(TEMP_DIR);
		$config->setProxyNamespace('TestProxy');
		$config->setDefaultRepositoryClassName(Kdyby\Doctrine\EntityRepository::class);
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver([__DIR__], FALSE));
		$em = Kdyby\Doctrine\EntityManager::create($connection, $config);
		if ($createSchema === FALSE) {
			return $em;
		}
		$schemaTool = new SchemaTool($em);
		$meta = $em->getMetadataFactory()->getAllMetadata();
		$schemaTool->createSchema($meta);

		return $em;

	}

}
