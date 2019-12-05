<?php

namespace Beecom\CustomStockStatus\Model;

use Beecom\CustomStockStatus\Model\ResourceModel\Rule as ResourceLabel;
use Beecom\CustomStockStatus\Api\Data\RuleInterface;
use Beecom\CustomStockStatus\Api\RuleRepositoryInterface;
use Beecom\CustomStockStatus\Api\Data\RuleSearchResultsInterfaceFactory;
use Beecom\CustomStockStatus\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LabelRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var ResourceLabel
     */
    protected $resource;

    /**
     * @var RuleFactory
     */
    protected $labelFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var RuleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * RuleRepository constructor.
     * @param ResourceLabel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param RuleFactory $ruleFactory
     * @param RuleCollectionFactory $ruleCollection
     * @param RuleSearchResultsInterfaceFactory $ruleSearchResults
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceLabel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        RuleFactory $ruleFactory,
        RuleCollectionFactory $ruleCollection,
        RuleSearchResultsInterfaceFactory $ruleSearchResults,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->labelFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollection;
        $this->searchResultsFactory = $ruleSearchResults;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @param RuleInterface $label
     * @return RuleInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(RuleInterface $label)
    {
        try {
            if ($label->getStoreId() === null) {
                $storeId = $this->storeManager->getStore()->getId();
                $label->setStoreId($storeId);
            }

            $this->resource->save($label);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $label;
    }

    /**
     * Load Label data by given Label Identity
     *
     * @param string $labelId
     * @return Rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($labelId)
    {
        $label = $this->labelFactory->create();
        $this->resource->load($label, $labelId);
        if (!$label->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $labelId));
        }
        return $label;
    }

    /**
     * Delete Label
     *
     * @param \Beecom\CustomStockStatus\Api\Data\RuleInterface $label
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RuleInterface $label)
    {
        try {
            $this->resource->delete($label);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Label by given Label Identity
     *
     * @param string $labelId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($labelId)
    {
        return $this->delete($this->getById($labelId));
    }

    /**
     * Load Page data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Beecom\CustomStockStatus\Api\Data\RuleSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Beecom\CustomStockStatus\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var RuleSearchResultsInterfaceFactory $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 101.1.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Beecom\CustomStockStatus\Model\Api\SearchCriteria\RuleCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
}
