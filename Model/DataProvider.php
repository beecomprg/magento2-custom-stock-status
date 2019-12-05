<?php

namespace Beecom\CustomStockStatus\Model;

use Beecom\CustomStockStatus\Api\Data\CustomStockInterface;
use Beecom\CustomStockStatus\Api\RuleRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;

    protected $searchCriteriaBuilder;

    protected $ruleRepository;

    protected $sortBuilder;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Api\SortOrderBuilder $sortBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortBuilder = $sortBuilder;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        /** @var CustomStockInterface $item */
        foreach ($items as $item) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(
                    'rule_id',
                    [$item->getId()],
                    'eq'
                )
                ->addSortOrder($this->sortBuilder->setField('position')
                    ->setAscendingDirection()->create())
                ->create();

            $rules = $this->ruleRepository->getList($searchCriteria);
            $foundRules = [];
            foreach ($rules->getItems() as $rule) {
                $foundRules[] = $rule->getData();
            }

            $item->setCustomstockstatusRuleFormContainer($foundRules);
            $this->loadedData[$item->getId()] = $item->getData();
        }

        return $this->loadedData;
    }
}
