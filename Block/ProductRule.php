<?php

namespace Beecom\CustomStockStatus\Block;

use Beecom\CustomStockStatus\Api\RuleRepositoryInterface;
use Beecom\CustomStockStatus\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ProductRule extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $ruleFactory;
    protected $ruleCollectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $_storeManager;
    protected $_customerSession;
    protected $helper;
    protected $searchCriteriaBuilder;
    protected $ruleRepository;
    protected $sortBuilder;

    /**
     * ProductRule constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Beecom\CustomStockStatus\Model\RuleFactory $ruleFactory
     * @param \Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Data $helper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RuleRepositoryInterface $ruleRepository
     * @param \Magento\Framework\Api\SortOrderBuilder $sortBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Beecom\CustomStockStatus\Model\RuleFactory $ruleFactory,
        \Beecom\CustomStockStatus\Model\ResourceModel\CustomStock\CollectionFactory $ruleCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        Data $helper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\Api\SortOrderBuilder $sortBuilder,
        array $data = []
    )
    {
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortBuilder = $sortBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @param null $product
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomStockStatus($product = null)
    {
        if (!$this->helper->isEnabled()) {
            return false;
        }

        if (!$product) {
            $product = $this->getProduct();
        }

        if (!$product) {
            $product = $this->_coreRegistry->registry('product');
        }

        $storeId = $this->_storeManager->getStore()->getId();

        $rulesApplied = $this->ruleCollectionFactory->create();
        $collection = $rulesApplied->getProductRules($product->getId(), $storeId);

        $customStockRule = $collection->getData();
        if (!$customStockRule) {
            return false;
        }

        $ruleId = $customStockRule['rule_id'];

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'rule_id',
                [$ruleId],
                'eq'
            )
            ->addSortOrder($this->sortBuilder->setField('position')
                ->setAscendingDirection()->create())
            ->create();

        $rules = $this->ruleRepository->getList($searchCriteria);
        $productQty = $product->getExtensionAttributes()->getStockItem()->getQty();

        $matchedRule = $this->getMatchedRule($rules->getItems(), $productQty);
        if($matchedRule == false) {
            return false;
        }
        
        return $this->replaceValues($matchedRule['text'], $productQty);
    }

    /**
     * @param $text
     * @param $productQty
     * @return mixed
     */
    protected function replaceValues($text, $productQty)
    {
        $replacements = ['{qty}'];
        $values   = [$productQty];

        return str_replace($replacements, $values, $text);
    }

    /**
     * @param $rules
     * @param $productQty
     * @return bool
     */
    protected function getMatchedRule($rules, $productQty)
    {
        foreach ($rules as $rule) {
            $data = $rule->getData();
            $ruleType = $data['rule_type'];
            switch ($ruleType) {
                case 5:
                    //Range
                    if ($productQty >= $data['qty_from'] && $productQty <= $data['qty_to']) {
                        return $data;
                    }
                    break;
                case 4:
                    //Equal or less than
                    if ($productQty <= $data['qty_from']) {
                        return $data;
                    }
                    break;
                case 3:
                    if ($productQty >= $data['qty_from']) {
                        return $data;
                    }
                    //Equal or larger than
                    break;
                case 2:
                    if ($productQty < $data['qty_from']) {
                        return $data;
                    }
                    //Less than
                    break;
                case 1:
                    if ($productQty > $data['qty_from']) {
                        return $data;
                    }
                    //Larger than
                    break;
                default:
                    return false;
                    break;
            }
        }

        return false;
    }
}
