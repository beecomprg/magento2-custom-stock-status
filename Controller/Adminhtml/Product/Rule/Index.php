<?php


namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule;


class Index extends \Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Custom Stock Status Rules'), __('Custom Stock Status Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Custom Stock Status Rules'));
        $this->_view->renderLayout('root');
    }
}
