<?php


namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule;


class Edit extends \Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Beecom\CustomStockStatus\Model\CustomStock $model */
        $model = $this->customStockFactory->create();

        if ($id) {
            $model = $this->customStockRepository->getById($id);
            if (!$model->getRuleId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('beecom_customstockstatus/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);

        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setFormName('customstockstatus_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );

        $this->coreRegistry->register('current_rule', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Label Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Rule')
        );

        $breadcrumb = $id ? __('Edit Rule') : __('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
