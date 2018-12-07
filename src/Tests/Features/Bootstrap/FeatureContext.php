<?php declare(strict_types = 1);

namespace Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Yaml\Yaml;

class FeatureContext implements Context
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var Configuration
     */
    private $annotationMetadataConfiguration;

    /**
     * @var array
     */
    private $connectionConfiguration;

    public function __construct()
    {
        $connectionConfiguration = $this->getConnectionConfiguration();
        $dbName = $connectionConfiguration['dbname'];
        unset($connectionConfiguration['dbname']);

        $tmpConnection = DriverManager::getConnection($connectionConfiguration);
        $tmpConnection->connect();

        if (in_array($dbName, $tmpConnection->getSchemaManager()->listDatabases())) {
            $tmpConnection->getSchemaManager()->dropDatabase($dbName);
        }

        if (!in_array($dbName, $tmpConnection->getSchemaManager()->listDatabases())) {
            $tmpConnection->getSchemaManager()->createDatabase($dbName);
        }
    }

    /**
     * @BeforeScenario
     */
    public function prepare()
    {
        $this->getSchemaTool()->updateSchema($this->getEntityManager()->getMetadataFactory()->getAllMetadata(), true);
    }

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        $this->getSchemaTool()->dropSchema($this->getEntityManager()->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        if (null === $this->entityManager) {
            $this->entityManager = EntityManager::create(
                $this->getConnectionConfiguration(),
                $this->getAnnotationMetadataConfiguration()
            );
        }

        return $this->entityManager;
    }

    /**
     * @return SchemaTool
     */
    private function getSchemaTool(): SchemaTool
    {
        if (null === $this->schemaTool) {
            $this->schemaTool = new SchemaTool($this->getEntityManager());
        }

        return $this->schemaTool;
    }

    /**
     * @return Configuration
     */
    private function getAnnotationMetadataConfiguration(): Configuration
    {
        if (null === $this->annotationMetadataConfiguration) {
            $this->annotationMetadataConfiguration = Setup::createAnnotationMetadataConfiguration([
                sprintf('%s/Entity', __DIR__),
            ], true, null, null, false);
        }

        return $this->annotationMetadataConfiguration;
    }

    /**
     * @return array
     */
    private function getConnectionConfiguration(): array
    {
        if (null === $this->connectionConfiguration) {
            $this->connectionConfiguration = Yaml::parse(file_get_contents(sprintf(
                '%s/Resources/config/doctrine.yaml',
                __DIR__
            )));
        }

        return $this->connectionConfiguration;
    }
}
