<?php


namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule;


class NewAction extends \Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
