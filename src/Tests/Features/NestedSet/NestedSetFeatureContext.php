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
    private $nodes = array();

    /**
     * @Given /^I want to create (\d+) root node entities with created at date "(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})"/
     *
     * @param int $count
     * @param string $createdAt
     */
    public function iWantToCreateNRootNodeEntitiesWithCreatedAtDate($count, $createdAt)
    {
        for ($counter = 0; $counter < (int) $count; $counter++) {
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
    public function iAddNChildrenWithCreatedAtDate($count, $createdAt)
    {
        foreach ($this->nodes as &$rootNode) {
            if (0 === $rootNode->getTreeLevel() && 0 === $rootNode->getTreeLeft()) {
                for ($counter = 0; $counter < (int) $count; $counter++) {
                    $this->nodes[] = $this
                        ->getRepository()
                        ->persistChildNode($this->createEntity($createdAt), $rootNode)
                    ;
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
    public function iAddNChildrenWithCreatedAtDateOfNodesWithLevel($count, $createdAt, $level)
    {
        foreach ($this->nodes as &$node) {
            if ((int) $level === $node->getTreeLevel()) {
                for ($counter = 0; $counter < (int) $count; $counter++) {
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
    public function iAddNChildrenWithCreatedAtDateAfterNodeWithId($count, $createdAt, $id)
    {
        foreach ($this->nodes as &$node) {
            if ((int) $id === $node->getId()) {
                for ($counter = 0; $counter < (int) $count; $counter++) {
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
    public function iAddNChildrenWithCreatedAtDateBeforeNodeWithId($count, $createdAt, $id)
    {
        foreach ($this->nodes as &$node) {
            if ((int) $id === $node->getId()) {
                for ($counter = 0; $counter < (int) $count; $counter++) {
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
     */
    public function iMoveNodeWithIdAfterNodeWithId($targetId, $idAfter)
    {
        $targetNode = null;
        $nodeAfter = null;

        foreach ($this->nodes as &$node) {
            if ((int) $targetId === $node->getId()) {
                $targetNode = $node;
            } elseif ((int) $idAfter === $node->getId()) {
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
     */
    public function iMoveNodeWithIdBeforeNodeWithId($targetId, $idBefore)
    {
        $targetNode = null;
        $nodeBefore = null;

        foreach ($this->nodes as &$node) {
            if ((int) $targetId === $node->getId()) {
                $targetNode = $node;
            } elseif ((int) $idBefore === $node->getId()) {
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
    public function iRemoveNodeWithId($id)
    {
        foreach ($this->nodes as $node) {
            if ((int) $id === $node->getId()) {
                $this->getRepository()->removeNode($node);
            }
        }
    }

    /**
     * @Given /^I load tree with scope id (\d+)/
     *
     * @param int $id
     */
    public function iLoadTreeWithScopeId($id)
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
    public function iShouldGetTheseRowsInDatabaseFilteredByScopeId(TableNode $expectedNodes, $filterScopeId)
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
    public function iShouldGetTheseRowsInDatabase(TableNode $expectedNodes, $filterScopeId = null)
    {
        /** @var NestedSet[] $nodes */
        $nodesQuery = $this
            ->getRepository()
            ->createQueryBuilder('ns')
            ->orderBy('ns.treeScopeId', 'ASC')
            ->orderBy('ns.treeLeft', 'ASC')
        ;

        if (null !== $filterScopeId) {
            $nodesQuery->where('ns.treeScopeId = :treeScopeId')->setParameter('treeScopeId', (int) $filterScopeId);
        }

        $nodes = $nodesQuery->getQuery()->getResult();

//        dump($nodes);
//        dump(count($expectedNodes->getIterator()));
//        dump(count($nodes));

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
    private function createEntity($createdAt)
    {
        $nestedSet = new NestedSet();

        return $nestedSet->setCreatedAt(new \DateTime($createdAt));
    }

    /**
     * @return NestedSetEntityRepository
     */
    private function getRepository()
    {
        if (null === $this->nestedSetEntityRepository) {
            $this->nestedSetEntityRepository = $this->getEntityManager()->getRepository(get_class(new NestedSet()));
        }

        return $this->nestedSetEntityRepository;
    }
}
