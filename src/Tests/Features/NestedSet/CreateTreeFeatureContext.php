<?php

namespace Janisbiz\DoctrineNestedSet\Tests\Features\NestedSet;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\Entity\NestedSet;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\FeatureContext;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\Repository\NestedSetRepository;

class CreateTreeFeatureContext extends FeatureContext
{
    private $rootNodes;

    /**
     * @Given /^I have (\d+) root node entities that's created at "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})"/
     *
     * @param string $createdAt
     */
    public function iHaveEntityThatsCreatedAt(int $count, string $createdAt)
    {
        $this->rootNodes = array_fill(
            0,
            $count,
            (new NestedSet())->setCreatedAt(new \DateTime($createdAt))
        );
    }

    /**
     * @When I create a new tree
     */
    public function iCreateANewTree()
    {
        foreach ($this->rootNodes as &$rootNode) {
            $this->getRepository()->persistRootNode($rootNode);
        }
    }

    /**
     * @Then I should get:
     *
     * @param TableNode $expectedRootNodes
     *
     * @throws \Exception
     */
    public function iShouldGet(TableNode $expectedRootNodes)
    {
        /** @var NestedSet[] $rootNodes */
        $rootNodes = $this->getRepository()->findAll();

        if (count($expectedRootNodes->getIterator()) !== count($rootNodes)) {
            throw new \Exception('Count of expected root nodes doesn\'t match count of persisted root nodes!');
        }

        foreach ($expectedRootNodes as $expectedRootNode) {
            foreach ($rootNodes as $rootNode) {
                if ($rootNode->getId() === (int) $expectedRootNode['id']) {
                    if ($rootNode->getTreeScopeId() !== (int) $expectedRootNode['tree_scope_id']
                        || $rootNode->getTreeLeft() !== (int) $expectedRootNode['tree_left']
                        || $rootNode->getTreeRight() !== (int) $expectedRootNode['tree_right']
                        || $rootNode->getTreeLevel() !== (int) $expectedRootNode['tree_level']
                        || $rootNode->getCreatedAt()->format('Y-m-d H:i:s') !== $expectedRootNode['created_at']
                    ) {
                        throw new \Exception('Data mismatch, when storing root node!');
                    }

                    continue 2;
                }
            }

            throw new \Exception('Could not found saved root node!');
        }
    }

    /**
     * @return NestedSetRepository
     */
    private function getRepository()
    {
        /** @var NestedSetRepository $nestedSetRepository */
        $nestedSetRepository = $this->getEntityManager()->getRepository(NestedSet::class);

        return $nestedSetRepository;
    }
}
