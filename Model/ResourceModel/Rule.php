<?php


namespace Beecom\CustomStockStatus\Model\ResourceModel;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;

class Rule extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    protected $entityManager;
    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteDynamicRows($id)
    {
        if (!$id) {
            return;
        }

        $connection = $this->getConnection();
        $connection->delete(
            $this->getMainTable(),
            ['rule_id = ?' => $id]
        );
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('beecom_custom_stock_status_rule', 'id');
    }
}
