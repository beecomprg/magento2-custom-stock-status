<?php

namespace Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\Relation\Store;

use Beecom\CustomStockStatus\Model\ResourceModel\CustomStock;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var CustomStock
     */
    protected $resourcePage;

    /**
     * @param MetadataPool $metadataPool
     * @param CustomStock $resourcePage
     */
    public function __construct(
        MetadataPool $metadataPool,
        CustomStock $resourcePage
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourcePage = $resourcePage;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourcePage->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
