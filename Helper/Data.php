<?php


namespace Beecom\CustomStockStatus\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('beecom_customstockstatus/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
