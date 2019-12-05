<?php

namespace Beecom\CustomStockStatus\Api\Data;

interface RuleInterface
{
    const ID = 'id';
    const RULE_ID = 'rule_id';
    const RULE_TYPE = 'rule_type';
    const QTY_FROM = 'qty_from';
    const QTY_TO = 'qty_to';
    const TEXT = 'text';

    public function getId();

    public function getRuleId();

    public function getRuleType();

    public function getQtyFrom();

    public function getQtyTo();

    public function getText();

    public function setId($id);

    public function setRuleId($ruleId);

    public function setRuleType($ruleType);

    public function setQtyFrom($qtyFrom);

    public function setQtyTo($qtyTo);

    public function setText($text);
}