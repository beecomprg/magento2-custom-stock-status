<?php
namespace Beecom\CustomStockStatus\Model;

use Beecom\CustomStockStatus\Api\Data\RuleInterface;
use Magento\Framework\Model\AbstractModel;

class Rule extends AbstractModel implements RuleInterface
{
    const CACHE_TAG = 'beecom_custom_stock_status_rule';
    protected $_cacheTag = 'beecom_custom_stock_status_rule';
    protected $_eventPrefix = 'beecom_custom_stock_status_rule';

    protected function _construct()
    {
        $this->_init('Beecom\CustomStockStatus\Model\ResourceModel\Rule');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    public function getQtyFrom()
    {
        return $this->getData(self::QTY_FROM);
    }

    public function getQtyTo()
    {
        return $this->getData(self::QTY_TO);
    }

    public function getRuleType()
    {
        return $this->getData(self::RULE_TYPE);
    }

    public function getText()
    {
        return $this->getData(self::TEXT);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    public function setQtyFrom($qtyFrom)
    {
        return $this->setData(self::QTY_FROM, $qtyFrom);
    }

    public function setQtyTo($qtyTo)
    {
        return $this->setData(self::QTY_TO, $qtyTo);
    }

    public function setRuleType($ruleType)
    {
        return $this->setData(self::RULE_TYPE, $ruleType);
    }

    public function setText($text)
    {
        return $this->setData(self::TEXT, $text);
    }
}
