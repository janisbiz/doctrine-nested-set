<?php

namespace Janisbiz\DoctrineNestedSet\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Janisbiz\DoctrineNestedSet\Entity\NestedSetEntityInterface;
use Janisbiz\DoctrineNestedSet\Exception\NestedSetException;

class NestedSetEntityRepository extends EntityRepository
{
    /**
     * @var bool
     */
    private $newTransaction = false;

    /**
     * @param NestedSetEntityInterface $node
     *
     * @return NestedSetEntityInterface
     * @throws NestedSetException
     */
    public function persistRootNode(NestedSetEntityInterface $node): NestedSetEntityInterface
    {
        $entityManager = $this->getEntityManager();

        try {
            $this->beginTransaction();

            $node
                ->setTreeLeft(0)
                ->setTreeRight(1)
                ->setTreeLevel(0)
            ;

            $entityManager->persist($node);
            $entityManager->flush($node);

            $node->setTreeScopeId($node->getId());

            $entityManager->persist($node);
            $entityManager->flush($node);

            $this->executeTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param NestedSetEntityInterface $of
     *
     * @return NestedSetEntityInterface
     */
    public function persistChildNode(
        NestedSetEntityInterface $node,
        NestedSetEntityInterface $of
    ): NestedSetEntityInterface {
        $this->reloadNodeNestedSetData($of);

        $node
            ->setTreeScopeId($of->getTreeScopeId())
            ->setTreeLeft($of->getTreeRight())
            ->setTreeRight($of->getTreeRight() + 1)
            ->setTreeLevel($of->getTreeLevel() + 1)
        ;

        $this->insertNode($node);
        $this->reloadNodeNestedSetData($of);

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param NestedSetEntityInterface $after
     *
     * @return NestedSetEntityInterface
     */
    public function persistNodeAfter(
        NestedSetEntityInterface $node,
        NestedSetEntityInterface $after
    ): NestedSetEntityInterface {
        $this->reloadNodeNestedSetData($after);

        $node
            ->setTreeScopeId($after->getTreeScopeId())
            ->setTreeLeft($after->getTreeRight() + 1)
            ->setTreeRight($after->getTreeRight() + 2)
            ->setTreeLevel($after->getTreeLevel())
        ;

        $this->insertNode($node);
        $this->reloadNodeNestedSetData($after);

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param NestedSetEntityInterface $before
     *
     * @return NestedSetEntityInterface
     */
    public function persistNodeBefore(
        NestedSetEntityInterface $node,
        NestedSetEntityInterface $before
    ): NestedSetEntityInterface {
        $this->reloadNodeNestedSetData($before);

        $node
            ->setTreeScopeId($before->getTreeScopeId())
            ->setTreeLeft($before->getTreeLeft())
            ->setTreeRight($before->getTreeLeft() + 1)
            ->setTreeLevel($before->getTreeLevel())
        ;

        $this->insertNode($node);
        $this->reloadNodeNestedSetData($before);

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param NestedSetEntityInterface $after
     *
     * @return bool
     * @throws NestedSetException
     */
    public function moveNodeAfter(NestedSetEntityInterface $node, NestedSetEntityInterface $after): bool
    {
        $this->reloadNodeNestedSetData($node);

        if ($this->hasChildren($node)) {
            throw new NestedSetException('Cannot move node if it has children!');
        }

        $entityManager = $this->getEntityManager();

        try {
            $this->beginTransaction();

            $this->updateLeftNodes($node, true)->updateRightNodes($node, true);

            $this->reloadNodeNestedSetData($node)->reloadNodeNestedSetData($after);
            $node
                ->setTreeLeft($after->getTreeRight() + 1)
                ->setTreeRight($after->getTreeRight() + 2)
                ->setTreeLevel($after->getTreeLevel())
            ;

            $entityManager->merge($node);
            $entityManager->flush($node);

            $this->updateLeftNodes($node)->updateRightNodes($node);

            $this->executeTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        $this->reloadNodeNestedSetData($node)->reloadNodeNestedSetData($after);

        return true;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param NestedSetEntityInterface $before
     *
     * @return NestedSetEntityInterface
     * @throws NestedSetException
     */
    public function moveNodeBefore(
        NestedSetEntityInterface $node,
        NestedSetEntityInterface $before
    ): NestedSetEntityInterface {
        $this->reloadNodeNestedSetData($node);

        if ($this->hasChildren($node)) {
            throw new NestedSetException('Cannot move node if it has children!');
        }

        $entityManager = $this->getEntityManager();

        try {
            $this->beginTransaction();

            $this->updateLeftNodes($node, true)->updateRightNodes($node, true);

            $this->reloadNodeNestedSetData($node)->reloadNodeNestedSetData($before);
            $node
                ->setTreeLeft($before->getTreeLeft())
                ->setTreeRight($before->getTreeLeft() + 1)
                ->setTreeLevel($before->getTreeLevel())
            ;

            $entityManager->merge($node);
            $entityManager->flush($node);

            $this->updateLeftNodes($node)->updateRightNodes($node);

            $this->executeTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        $this->reloadNodeNestedSetData($node)->reloadNodeNestedSetData($before);

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     *
     * @return bool
     */
    public function removeNode(NestedSetEntityInterface $node): bool
    {
        $entityManager = $this->getEntityManager();

        try {
            $this->beginTransaction();

            $this->reloadNodeNestedSetData($node);

            foreach ($this->getChildren($node) as $child) {
                $entityManager->remove($child);
                $entityManager->flush($child);
            }

            $this->updateLeftNodes($node, true)->updateRightNodes($node, true);

            $entityManager->remove($node);
            $entityManager->flush($node);

            $this->executeTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        return true;
    }

    /**
     * @param int $treeScopeId
     *
     * @return NestedSetEntityInterface[]
     */
    public function loadTree($treeScopeId): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('tree')
            ->from($this->getClassName(), 'tree')
            ->where('tree.treeScopeId = :treeScopeId')
            ->setParameter('treeScopeId', $treeScopeId)
            ->orderBy('tree.treeLeft', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $treeScopeId
     *
     * @return $this
     */
    public function removeTree($treeScopeId): NestedSetEntityRepository
    {
        $entityManager = $this->getEntityManager();
        $tree = $this->loadTree($treeScopeId);

        foreach ($tree as $i => $node) {
            if ($node->getId() === $node->getTreeScopeId()) {
                continue;
            }

            $entityManager->remove($node);
            unset($tree[$i]);
        }

        $entityManager->flush();

        foreach ($tree as $node) {
            $node->setTreeScopeId(null);
            $entityManager->persist($node);
            $entityManager->flush();
            $entityManager->remove($node);
            $entityManager->flush();
        }

        return $this;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param bool $includeNode
     *
     * @return NestedSetEntityInterface[]
     */
    public function getChildren(NestedSetEntityInterface $node, $includeNode = false): array
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('tree')
            ->from($this->getClassName(), 'tree')
            ->where('tree.treeScopeId = :treeScopeId')
        ;

        if (true === $includeNode) {
            $qb->andWhere('(tree.treeLeft >= :treeLeft AND tree.treeRight <= :treeRight)');
        } else {
            $qb->andWhere('(tree.treeLeft > :treeLeft AND tree.treeRight < :treeRight)');
        }

        return $qb
            ->setParameters([
                'treeScopeId' => $node->getTreeScopeId(),
                'treeLeft' => $node->getTreeLeft(),
                'treeRight' => $node->getTreeRight(),
            ])
            ->groupBy('tree.id')
            ->orderBy('tree.treeLeft', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param NestedSetEntityInterface $node
     *
     * @return bool
     */
    public function hasChildren(NestedSetEntityInterface $node): bool
    {
        return $node->getTreeRight() - $node->getTreeLeft() > 1;
    }

    /**
     * @param NestedSetEntityInterface $node
     *
     * @return NestedSetEntityRepository
     */
    private function reloadNodeNestedSetData(NestedSetEntityInterface &$node): NestedSetEntityRepository
    {
        /** @var NestedSetEntityInterface|null $nodeReloaded */
        if (null === ($node = $this->find($node->getId()))) {
            throw new NestedSetException('Could not reload node!');
        }

        return $this;
    }

    /**
     * @param NestedSetEntityInterface $node
     *
     * @return NestedSetEntityInterface
     */
    private function insertNode(NestedSetEntityInterface $node): NestedSetEntityInterface
    {
        $entityManager = $this->getEntityManager();

        try {
            $this->beginTransaction();

            $entityManager->persist($node);
            $entityManager->flush($node);

            $this->updateLeftNodes($node)->updateRightNodes($node);

            $this->executeTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        return $node;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param bool $delete
     *
     * @return $this
     */
    private function updateLeftNodes(NestedSetEntityInterface $node, $delete = false): NestedSetEntityRepository
    {
        $entityManager = $this->getEntityManager();

        /** @var QueryBuilder $leftNodesToUpdateQb */
        $leftNodesToUpdateQb = $this->createQueryBuilder('tree');
        /** @var NestedSetEntityInterface[] $leftNodesToUpdate */
        $leftNodesToUpdate = $leftNodesToUpdateQb
            ->where('tree.treeScopeId = :treeScopeId')
            ->andWhere('tree.treeLeft >= :treeLeft')
            ->andWhere('tree.id != :id')
            ->setParameters([
                'treeScopeId' => $node->getTreeScopeId(),
                'treeLeft' => $node->getTreeLeft(),
                'id' => $node->getId(),
            ])
            ->getQuery()
            ->getResult()
        ;

        foreach ($leftNodesToUpdate as $i => $leftNodeToUpdate) {
            $leftNodeToUpdate->setTreeLeft(
                $leftNodeToUpdate->getTreeLeft()
                + (($node->getTreeRight() - $node->getTreeLeft() + 1) * ($delete ? -1 : 1))
            );

            $entityManager->persist($leftNodeToUpdate);
        }

        $entityManager->flush();

        return $this;
    }

    /**
     * @param NestedSetEntityInterface $node
     * @param bool $delete
     *
     * @return $this
     */
    private function updateRightNodes(NestedSetEntityInterface $node, $delete = false): NestedSetEntityRepository
    {
        $entityManager = $this->getEntityManager();

        /** @var QueryBuilder $rightNodesToUpdateQb */
        $rightNodesToUpdateQb = $this->createQueryBuilder('tree');
        /** @var NestedSetEntityInterface[] $rightNodesToUpdate */
        $rightNodesToUpdate = $rightNodesToUpdateQb
            ->where('tree.treeScopeId = :treeScopeId')
            ->andWhere('tree.treeRight >= :treeRight')
            ->andWhere('tree.id != :id')
            ->setParameters([
                'treeScopeId' => $node->getTreeScopeId(),
                'treeRight' => $node->getTreeRight() - ($delete ? 0 : 1),
                'id' => $node->getId(),
            ])
            ->getQuery()
            ->getResult()
        ;

        foreach ($rightNodesToUpdate as $i => $rightNodeToUpdate) {
            $rightNodeToUpdate->setTreeRight(
                $rightNodeToUpdate->getTreeRight()
                + (($node->getTreeRight() - $node->getTreeLeft() + 1) * ($delete ? -1 : 1))
            );

            $entityManager->persist($rightNodeToUpdate);
        }

        $entityManager->flush();

        return $this;
    }

    /**
     * @return $this
     */
    private function beginTransaction(): NestedSetEntityRepository
    {
        $entityManager = $this->getEntityManager();

        $this->newTransaction = false;
        if (!$entityManager->getConnection()->isTransactionActive()) {
            $entityManager->getConnection()->beginTransaction();
            $this->newTransaction = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function executeTransaction(): NestedSetEntityRepository
    {
        if (true === $this->newTransaction) {
            $this->getEntityManager()->getConnection()->commit();
            /** Flushing EM, as bulk inserts are causing super huge performance issues on Doctrine */
            $this->getEntityManager()->clear();
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function rollbackTransaction(): NestedSetEntityRepository
    {
        if (true === $this->newTransaction) {
            $this->getEntityManager()->getConnection()->rollBack();
        }

        return $this;
    }
}
