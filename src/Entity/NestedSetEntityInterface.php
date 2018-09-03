<?php

namespace Janisbiz\DoctrineNestedSet\Entity;

interface NestedSetEntityInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getTreeScopeId();

    /**
     * @param int|null $treeScopeId
     *
     * @return $this
     */
    public function setTreeScopeId($treeScopeId);

    /**
     * @return int
     */
    public function getTreeLeft();

    /**
     * @param int $treeLeft
     *
     * @return $this
     */
    public function setTreeLeft($treeLeft);

    /**
     * @return int
     */
    public function getTreeRight();

    /**
     * @param int $treeRight
     *
     * @return $this
     */
    public function setTreeRight($treeRight);

    /**
     * @return int
     */
    public function getTreeLevel();

    /**
     * @param int $treeLevel
     *
     * @return $this
     */
    public function setTreeLevel($treeLevel);
}
