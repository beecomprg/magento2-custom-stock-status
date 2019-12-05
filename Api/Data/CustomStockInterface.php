<?php

namespace Beecom\CustomStockStatus\Api\Data;

/**
 * Brands page interface.
 */
interface CustomStockInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'rule_id';
    const IS_ACTIVE = 'is_active';
    const NAME = 'name';
    const SORT_ORDER = 'sort_order';

    /**#@-*/
    public function getId();

    /**
     * Get ID
     *
     * @return bool|null
     */
    public function getIsActive();

    public function getName();

    public function getRuleId();

    public function getSortOrder();

    /**
     * @param $id
     * @return mixed
     */
    public function setRuleId($id);

    /**
     * @param $isActive
     * @return mixed
     */
    public function setIsActive($isActive);

    public function setName($name);

    public function setSortOrder($sortOrder);
}
