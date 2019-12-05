<?php

namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product;

use Beecom\CustomStockStatus\Api\CustomStockRepositoryInterface;
use Beecom\CustomStockStatus\Api\RuleRepositoryInterface;

abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Beecom\CustomStockStatus\Model\CustomStockFactory
     */
    protected $customStockFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomStockRepositoryInterface
     */
    protected $customStockRepository;

    /**
     * @var \Beecom\CustomStockStatus\Model\ResourceModel\CustomStockFactory
     */
    protected $customStockFactoryResource;

    /**
     * @var \Beecom\CustomStockStatus\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var \Beecom\CustomStockStatus\Model\ResourceModel\RuleFactory
     */
    protected $ruleFactoryResource;

    protected $resource;

    /**
     * Rule constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Beecom\CustomStockStatus\Model\CustomStockFactory $customStockFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param CustomStockRepositoryInterface $customStockRepository
     * @param \Beecom\CustomStockStatus\Model\ResourceModel\CustomStockFactory $customStockFactoryResource
     * @param \Beecom\CustomStockStatus\Model\RuleFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param \Beecom\CustomStockStatus\Model\ResourceModel\RuleFactory $ruleFactoryResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Beecom\CustomStockStatus\Model\CustomStockFactory $customStockFactory,
        \Psr\Log\LoggerInterface $logger,
        CustomStockRepositoryInterface $customStockRepository,
        \Beecom\CustomStockStatus\Model\ResourceModel\CustomStockFactory $customStockFactoryResource,
        \Beecom\CustomStockStatus\Model\RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        \Beecom\CustomStockStatus\Model\ResourceModel\RuleFactory $ruleFactoryResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->dateFilter = $dateFilter;
        $this->customStockFactory = $customStockFactory;
        $this->logger = $logger;
        $this->customStockRepository = $customStockRepository;
        $this->customStockFactoryResource = $customStockFactoryResource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactoryResource = $ruleFactoryResource;
        $this->resource = $resourceConnection;
    }

    /**
     * Initiate rule
     *
     * @return void
     */
    protected function _initRule()
    {
        $rule = $this->customStockFactory->create();
        $this->coreRegistry->register(
            'current_rule',
            $rule
        );
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            $this->coreRegistry->registry('current_rule')->load($id);
        }
    }

    /**
     * Initiate action
     *
     * @return Rule
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Beecom_CustomStockStatus::rule')
            ->_addBreadcrumb(__('Custom Stock Status Rules'), __('Custom Stock Status Rules'));
        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Beecom_CustomStockStatus::rules');
    }
}
