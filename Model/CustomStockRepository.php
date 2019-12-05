<?php

namespace Beecom\CustomStockStatus\Model;

use Beecom\CustomStockStatus\Model\ResourceModel\CustomStock as ResourceLabel;
use Beecom\CustomStockStatus\Api\Data\CustomStockInterface;
use Beecom\CustomStockStatus\Api\CustomStockRepositoryInterface;
use Beecom\CustomStockStatus\Api\Data\CustomStockSearchResultsInterfaceFactory;
use Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\CollectionFactory as CustomStockCollectionFactory;
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
class CustomStockRepository implements CustomStockRepositoryInterface
{
    /**
     * @var ResourceLabel
     */
    protected $resource;

    /**
     * @var CustomStockFactory
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
     * @var CustomStockCollectionFactory
     */
    private $customStockCollectionFactory;

    /**
     * @var CustomStockSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * CustomStockRepository constructor.
     * @param ResourceLabel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CustomStockFactory $ruleFactory
     * @param CustomStockCollectionFactory $ruleCollection
     * @param CustomStockSearchResultsInterfaceFactory $ruleSearchResults
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceLabel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CustomStockFactory $customStockFactory,
        CustomStockCollectionFactory $customStockCollection,
        CustomStockSearchResultsInterfaceFactory $customStockSearchResults,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->labelFactory = $customStockFactory;
        $this->customStockCollectionFactory = $customStockCollection;
        $this->searchResultsFactory = $customStockSearchResults;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @param CustomStockInterface $label
     * @return CustomStockInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(CustomStockInterface $label)
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
     * @return CustomStock
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
     * @param \Beecom\CustomStockStatus\Api\Data\CustomStockInterface $label
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CustomStockInterface $label)
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
     * @return \Beecom\CustomStockStatus\Api\Data\CustomStockSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\Collection $collection */
        $collection = $this->customStockCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var CustomStockSearchResultsInterfaceFactory $searchResults */
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
                'Beecom\CustomStockStatus\Model\Api\SearchCriteria\CustomStockCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
}
