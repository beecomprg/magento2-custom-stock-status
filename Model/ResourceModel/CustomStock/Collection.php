<?php


namespace Beecom\CustomStockStatus\Model\ResourceModel\CustomStock;
use Beecom\CustomStockStatus\Api\Data\CustomStockInterface;
use Magento\Store\Model\Store;


class Collection extends \Magento\Cms\Model\ResourceModel\AbstractCollection
{
    protected $_idFieldName = 'rule_id';

    protected function _construct()
    {
        $this->_init(
            'Beecom\CustomStockStatus\Model\CustomStock',
            'Beecom\CustomStockStatus\Model\ResourceModel\CustomStock'
        );

    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    public function getProductRules($productId, $storeId)
    {
        $joinConditions = 'main_table.rule_id = beecom_custom_stock_status_applied.rule_id';
        $this->getSelect()->join(
            ['beecom_custom_stock_status_applied'],
            $joinConditions,
            []
        )->where("beecom_custom_stock_status_applied.product_id=".$productId)->where('beecom_custom_stock_status_applied.store_id IN ('.$storeId.', 0)')->order('sort_order', 'DESC');

        return $this->getFirstItem();
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


    /**
     * @return \Magento\Cms\Model\ResourceModel\AbstractCollection
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(CustomStockInterface::class);
        $this->performAfterLoad('beecom_custom_stock_status_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['beecom_custom_stock_status_store' => $this->getTable($tableName)])
                ->where('beecom_custom_stock_status_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);

            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }

                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }
}
