<?php

namespace Janisbiz\DoctrineNestedSet\Tests\Features\NestedSet;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\DoctrineNestedSet\Repository\NestedSetEntityRepository;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\Entity\NestedSet;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\FeatureContext;

class NestedSetFeatureContext extends FeatureContext
{
    /**
     * @var NestedSetEntityRepository
     */
    private $nestedSetEntityRepository;

    /**
     * @var NestedSet[]
     */
    private $nodes;

    /**
     * @Given /^I want to create (\d+) root node entities with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})"/
     *
     * @param int $count
     * @param string $createdAt
     */
    public function iWantToCreateNRootNodeEntitiesWithCreatedAtDate(int $count, string $createdAt)
    {
        for ($counter = 0; $counter < $count; $counter++) {
            $this->nodes[] = $this->createEntity($createdAt);
        }
    }

    /**
     * @When I create a new tree root nodes
     */
    public function iCreateANewTreeRootNodes()
    {
        foreach ($this->nodes as &$rootNode) {
            $this->getRepository()->persistRootNode($rootNode);
        }
    }

    /**
     * @Given /^I add (\d+) children with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})" on root node/
     *
     * @param int $count
     * @param string $createdAt
     */
    public function iAddNChildrenWithCreatedAtDate(int $count, string $createdAt)
    {
        foreach ($this->nodes as &$rootNode) {
            if ($rootNode->getTreeLevel() === 0 && $rootNode->getTreeLeft() === 0) {
                for ($counter = 0; $counter < $count; $counter++) {
                    $this->nodes[] = $this->getRepository()->persistChildNode(
                        $this->createEntity($createdAt),
                        $rootNode
                    );
                }
            }
        }
    }

    /**
     * @Given /^I add (\d+) children with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})" of nodes with level \
     *        (\d+)/
     *
     * @param int $count
     * @param string $createdAt
     * @param int $level
     */
    public function iAddNChildrenWithCreatedAtDateOfNodesWithLevel(int $count, string $createdAt, int $level)
    {
        foreach ($this->nodes as &$node) {
            if ($node->getTreeLevel() === $level) {
                for ($counter = 0; $counter < $count; $counter++) {
                    $this->nodes[] = $this->getRepository()->persistChildNode($this->createEntity($createdAt), $node);
                }
            }
        }
    }

    /**
     * @Given /^I add (\d+) children with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})" after node with id \
     *        (\d+)/
     *
     * @param int $count
     * @param string $createdAt
     * @param int $id
     */
    public function iAddNChildrenWithCreatedAtDateAfterNodeWithId(int $count, string $createdAt, int $id)
    {
        foreach ($this->nodes as &$node) {
            if ($node->getId() === $id) {
                for ($counter = 0; $counter < $count; $counter++) {
                    $this->nodes[] = $this->getRepository()->persistNodeAfter($this->createEntity($createdAt), $node);
                }
            }
        }
    }

    /**
     * @Given /^I add (\d+) children with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})" before node with id \
     *        (\d+)/
     *
     * @param int $count
     * @param string $createdAt
     * @param int $id
     */
    public function iAddNChildrenWithCreatedAtDateBeforeNodeWithId(int $count, string $createdAt, int $id)
    {
        foreach ($this->nodes as &$node) {
            if ($node->getId() === $id) {
                for ($counter = 0; $counter < $count; $counter++) {
                    $this->nodes[] = $this->getRepository()->persistNodeBefore($this->createEntity($createdAt), $node);
                }
            }
        }
    }

    /**
     * @Given /^I move node with id (\d+) after node with id (\d+)/
     *
     * @param int $targetId
     * @param int $idAfter
     *
     * @throws \Exception
     */
    public function iMoveNodeWithIdAfterNodeWithId(int $targetId, int $idAfter)
    {
        $targetNode = null;
        $nodeAfter = null;

        foreach ($this->nodes as &$node) {
            if ($targetId === $node->getId()) {
                $targetNode = $node;
            } elseif ($idAfter === $node->getId()) {
                $nodeAfter = $node;
            }
        }

        if (null === $targetNode || null === $nodeAfter) {
            throw new \Exception('Could not find target node or node after!');
        }

        $this->getRepository()->moveNodeAfter($targetNode, $nodeAfter);
    }

    /**
     * @Given /^I move node with id (\d+) before node with id (\d+)/
     *
     * @param int $targetId
     * @param int $idBefore
     *
     * @throws \Exception
     */
    public function iMoveNodeWithIdBeforeNodeWithId(int $targetId, int $idBefore)
    {
        $targetNode = null;
        $nodeBefore = null;

        foreach ($this->nodes as &$node) {
            if ($targetId === $node->getId()) {
                $targetNode = $node;
            } elseif ($idBefore === $node->getId()) {
                $nodeBefore = $node;
            }
        }

        if (null === $targetNode || null === $nodeBefore) {
            throw new \Exception('Could not find target node or node before!');
        }

        $this->getRepository()->moveNodeBefore($targetNode, $nodeBefore);
    }

    /**
     * @Given /^I remove node with id (\d+)/
     *
     * @param int $id
     */
    public function iRemoveNodeWithId(int $id)
    {
        foreach ($this->nodes as $node) {
            if ($id === $node->getId()) {
                $this->getRepository()->removeNode($node);
            }
        }
    }

    /**
     * @Given /^I load tree with scope id (\d+)/
     *
     * @param int $id
     */
    public function iLoadTreeWithScopeId(int $id)
    {
        $this->nodes = $this->getRepository()->loadTree($id);
    }

    /**
     * @Then /^I should get these rows in database filtered by scope id (\d+):$/
     *
     * @param TableNode $expectedNodes
     * @param int $filterScopeId
     *
     * @throws \Exception
     */
    public function iShouldGetTheseRowsInDatabaseFilteredByScopeId(TableNode $expectedNodes, int $filterScopeId)
    {
        $this->iShouldGetTheseRowsInDatabase($expectedNodes, $filterScopeId);
    }

    /**
     * @Then /^I should get these rows in database:$/
     *
     * @param TableNode $expectedNodes
     * @param null|int $filterScopeId
     *
     * @throws \Exception
     */
    public function iShouldGetTheseRowsInDatabase(TableNode $expectedNodes, int $filterScopeId = null)
    {
        /** @var NestedSet[] $nodes */
        $nodesQuery = $this
            ->getRepository()
            ->createQueryBuilder('ns')
            ->orderBy('ns.treeScopeId', 'ASC')
            ->orderBy('ns.treeLeft', 'ASC')
        ;

        if (null !== $filterScopeId) {
            $nodesQuery->where('ns.treeScopeId = :treeScopeId')->setParameter('treeScopeId', $filterScopeId);
        }

        $nodes = $nodesQuery->getQuery()->getResult();

        if (count($expectedNodes->getIterator()) !== count($nodes)) {
            throw new \Exception('Count of expected nodes doesn\'t match count of persisted nodes!');
        }

        foreach ($expectedNodes as $expectedNode) {
            foreach ($nodes as $node) {
                if ($node->getId() === (int) $expectedNode['id']) {
                    if ($node->getTreeScopeId() !== (int) $expectedNode['tree_scope_id']
                        || $node->getTreeLeft() !== (int) $expectedNode['tree_left']
                        || $node->getTreeRight() !== (int) $expectedNode['tree_right']
                        || $node->getTreeLevel() !== (int) $expectedNode['tree_level']
                        || $node->getCreatedAt()->format('Y-m-d H:i:s') !== $expectedNode['created_at']
                    ) {
                        throw new \Exception('Data mismatch, when storing node!');
                    }

                    continue 2;
                }
            }

            throw new \Exception('Could not found persisted node!');
        }
    }

    /**
     * @param string $createdAt
     *
     * @return NestedSet
     */
    private function createEntity(string $createdAt): NestedSet
    {
        return (new NestedSet())->setCreatedAt(new \DateTime($createdAt));
    }

    /**
     * @return NestedSetEntityRepository
     */
    private function getRepository(): NestedSetEntityRepository
    {
        if (null === $this->nestedSetEntityRepository) {
            $this->nestedSetEntityRepository = $this->getEntityManager()->getRepository(NestedSet::class);
        }

        return $this->nestedSetEntityRepository;
    }
}
