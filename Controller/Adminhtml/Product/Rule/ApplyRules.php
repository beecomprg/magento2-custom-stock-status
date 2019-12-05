<?php

namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule;

use Beecom\CustomStockStatus\Api\CustomStockRepositoryInterface;
use Beecom\CustomStockStatus\Model\ResourceModel\CustomStock;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ApplyRules extends \Magento\Backend\App\Action
{
    /**
     * SearchCriteria builder
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    protected $customStockRepository;

    protected $resource;

    protected $customStockResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CustomStockRepositoryInterface $customStockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
       \Magento\Framework\App\ResourceConnection $resourceConnection,
        CustomStock $customStockResource
    )
    {
        $this->customStockRepository = $customStockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resource = $resourceConnection;
        $this->customStockResource = $customStockResource;

        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $collection = $this->customStockRepository->getList($searchCriteria)->getItems();

        $this->clearAppliedRules();

        foreach ($collection as $item) {
            if (!$item->getIsActive()) {
                continue;
            }

            $ruleId = $item->getRuleId();

            $stores = $this->customStockResource->lookupStoreIds($ruleId);

            $matchedProductIds = $this->getRuleModel()->setData('conditions_serialized', $item->getData('conditions_serialized'))->getListProductIdsInRule();
            foreach (array_unique($matchedProductIds) as $id) {
                foreach ($stores as $store) {
                    $this->saveAppliedRule($ruleId, $id, $store);
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addSuccessMessage(__("Rules have been applied"));
        $customerBeforeAuthUrl = $this->_url->getUrl('beecom_customstockstatus/product_rule');

        return $resultRedirect->setPath($customerBeforeAuthUrl);
    }

    private function getRuleModel()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create('Beecom\CustomStockStatus\Model\CustomStock');
    }

    private function clearAppliedRules()
    {
        $connection = $this->resource->getConnection();

        $query = sprintf('delete from beecom_custom_stock_status_applied');
        $connection->query($query);
    }

    private function saveAppliedRule($ruleId, $productId, $storeId)
    {
        $connection = $this->resource->getConnection();

        $query = "insert into beecom_custom_stock_status_applied (rule_id, product_id, store_id) values ($ruleId, $productId, $storeId)";
        $connection->query($query);
    }
}
