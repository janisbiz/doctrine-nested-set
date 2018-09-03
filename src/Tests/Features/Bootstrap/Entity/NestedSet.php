<?php

namespace Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\Entity;

use Doctrine\ORM\Mapping as ORM;
use Janisbiz\DoctrineNestedSet\Entity\NestedSetEntityInterface;

/**
 * @ORM\Entity(repositoryClass="Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\Repository\NestedSetRepository")
 * @ORM\Table(name="tree")
 */
class NestedSet implements NestedSetEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tree_scope_id", type="integer", nullable=true)
     */
    private $treeScopeId;

    /**
     * @var int
     *
     * @ORM\Column(name="tree_left", type="integer")
     */
    private $treeLeft;

    /**
     * @var int
     *
     * @ORM\Column(name="tree_right", type="integer")
     */
    private $treeRight;

    /**
     * @var int
     *
     * @ORM\Column(name="tree_level", type="integer")
     */
    private $treeLevel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): NestedSet
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getTreeScopeId()
    {
        return $this->treeScopeId;
    }

    /**
     * @param int|null $treeScopeId
     *
     * @return $this
     */
    public function setTreeScopeId($treeScopeId): NestedSet
    {
        $this->treeScopeId = $treeScopeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTreeLeft(): int
    {
        return $this->treeLeft;
    }

    /**
     * @param int $treeLeft
     *
     * @return $this
     */
    public function setTreeLeft(int $treeLeft): NestedSet
    {
        $this->treeLeft = $treeLeft;

        return $this;
    }

    /**
     * @return int
     */
    public function getTreeRight(): int
    {
        return $this->treeRight;
    }

    /**
     * @param int $treeRight
     *
     * @return $this
     */
    public function setTreeRight(int $treeRight): NestedSet
    {
        $this->treeRight = $treeRight;

        return $this;
    }

    /**
     * @return int
     */
    public function getTreeLevel(): int
    {
        return $this->treeLevel;
    }

    /**
     * @param int $treeLevel
     *
     * @return $this
     */
    public function setTreeLevel(int $treeLevel): NestedSet
    {
        $this->treeLevel = $treeLevel;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): NestedSet
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
