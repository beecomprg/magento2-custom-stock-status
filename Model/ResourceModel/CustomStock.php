<?php

namespace Beecom\CustomStockStatus\Model\ResourceModel;

use Beecom\CustomStockStatus\Api\Data\CustomStockInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;

class CustomStock extends AbstractDb
{
    protected $metadataPool;

    protected $entityManager;

    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        $connectionName = null
    )
    {
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('beecom_custom_stock_status', 'rule_id');
    }

    /**
     * @param $ruleId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function lookupStoreIds($ruleId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(CustomStockInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('beecom_custom_stock_status_store')], 'store_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :rule_id');

        return $connection->fetchCol($select, ['rule_id' => (int)$ruleId]);
    }

    /**
     * @param $ruleId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function lookupRuleIds($ruleId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(CustomStockInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('beecom_custom_stock_status_rule')], 'rule_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :rule_id');

        return $connection->fetchCol($select, ['rule_id' => (int)$ruleId]);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        $ruleId = $this->getRuleId($object, $value, $field);
        if ($ruleId) {
            $this->entityManager->load($object, $ruleId);
        }
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param $value
     * @param null $field
     * @return bool|int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRuleId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(CustomStockInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $ruleId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $ruleId = count($result) ? $result[0] : false;
        }

        return $ruleId;
    }
}
