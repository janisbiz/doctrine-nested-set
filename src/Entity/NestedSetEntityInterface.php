<?php

namespace Janisbiz\DoctrineNestedSet\Entity;

interface NestedSetEntityInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id);

    /**
     * @return int
     */
    public function getTreeScopeId(): ?int;

    /**
     * @param int|null $treeScopeId
     *
     * @return $this
     */
    public function setTreeScopeId(?int $treeScopeId);

    /**
     * @return int
     */
    public function getTreeLeft(): int;

    /**
     * @param int $treeLeft
     *
     * @return $this
     */
    public function setTreeLeft(int $treeLeft);

    /**
     * @return int
     */
    public function getTreeRight(): int;

    /**
     * @param int $treeRight
     *
     * @return $this
     */
    public function setTreeRight(int $treeRight);

    /**
     * @return int
     */
    public function getTreeLevel(): int;

    /**
     * @param int $treeLevel
     *
     * @return $this
     */
    public function setTreeLevel(int $treeLevel);
}
