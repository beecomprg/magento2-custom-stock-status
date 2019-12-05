<?php

namespace Beecom\CustomStockStatus\Model\ResourceModel\Rule;

use Beecom\CustomStockStatus\Api\Data\RuleInterface;
use Magento\Store\Model\Store;

class Collection extends \Magento\Cms\Model\ResourceModel\AbstractCollection
{
    protected $_previewFlag;
    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Beecom\CustomStockStatus\Model\Rule', 'Beecom\CustomStockStatus\Model\ResourceModel\Rule');
    }

    /**
     * Filter collection by specified date.
     * Filter collection to only active rules.
     *
     * @param string|null $now
     * @use $this->addStoreGroupDateFilter()
     * @return $this
     */
    public function setValidationFilter($now = null)
    {
        if (!$this->getFlag('validation_filter')) {
            $this->addDateFilter($now);
            $this->setOrder('sort_order', self::SORT_ORDER_DESC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }

    /**
     * From date or to date filter
     *
     * @param $now
     * @return $this
     */
    public function addDateFilter($now)
    {
        $this->getSelect()->where(
            'from_date is null or from_date <= ?',
            $now
        )->where(
            'to_date is null or to_date >= ?',
            $now
        );

        return $this;
    }

    public function getProductRules($productId, $storeId, $customerGroupId)
    {
        $joinConditions = 'main_table.rule_id = beecom_custom_stock_status_applied.rule_id';
        $this->getSelect()->join(
            ['beecom_custom_stock_status_applied'],
            $joinConditions,
            []
        )->where("beecom_custom_stock_status_applied.product_id=".$productId)->where('beecom_custom_stock_status_applied.store_id IN ('.$storeId.', 0)')->order('sort_order', 'DESC');

        return $this;
    }

    public function getCustomStockRules($ruleId)
    {
        $joinConditions = 'main_table.rule_id = beecom_custom_stock_status_rule.rule_id';
        $this->getSelect()->join(
            ['beecom_custom_stock_status_rule'],
            $joinConditions,
            []
        )->where("beecom_custom_stock_status_rule.rule_id=".$ruleId)->order('position', 'DESC');

        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }
}
