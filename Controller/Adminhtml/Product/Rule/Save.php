<?php

namespace Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule;

class Save extends \Beecom\CustomStockStatus\Controller\Adminhtml\Product\Rule
{
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('beecom_customstockstatus/*/');
        }

        try {
            /** @var $model \Beecom\CustomStockStatus\Model\CustomStock */
            $model = $this->customStockFactory->create();
            $this->_eventManager->dispatch(
                'adminhtml_controller_beecom_customstockstatus_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue();

            $id = $this->getRequest()->getParam('rule_id');
            if ($id) {
                $model = $this->customStockRepository->getById($id);
            }

            $data = array_filter($data, function($value) { return $value !== ''; });

            $data = $this->prepareData($data);
            $model->loadPost($data);
            $model->setData($data);

            $this->_session->setPageData($model->getData());

            $this->_eventManager->dispatch(
                'beecom_customstockstatus_rule_prepare_save',
                ['rule' => $model, 'request' => $this->getRequest()]
            );

            $this->customStockRepository->save($model);

            $dynamicRowResource = $this->ruleFactoryResource->create();
            $dynamicRowData = $this->getRequest()->getParam('customstockstatus_rule_form_container');
            $dynamicRowResource->deleteDynamicRows($id);

            if (is_array($dynamicRowData) && !empty($dynamicRowData)) {
                foreach ($dynamicRowData as $dynamicRowDatum) {
                    if ($dynamicRowDatum['rule_type'] != 5) {
                        $dynamicRowDatum['qty_to'] = 0;
                    } else {
                        if (empty($dynamicRowDatum['qty_to'])) {
                            $dynamicRowDatum['qty_to'] = $dynamicRowDatum['qty_from'] + 1;
                        }
                    }
                    $dynamicRowDatum['ruleId'] = $model->getRuleId();
                    $this->saveRule($dynamicRowDatum);
                }
            }

            $this->messageManager->addSuccessMessage(__('You saved the rule.'));
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('beecom_customstockstatus/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('beecom_customstockstatus/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('rule_id');
            if (!empty($id)) {
                $this->_redirect('beecom_customstockstatus/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('beecom_customstockstatus/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the rule data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('beecom_customstockstatus/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
            return;
        }
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }

        unset($data['conditions_serialized']);

        return $data;
    }

    protected function saveRule($data)
    {
        $text = $data['text'];
        $qtyFrom = $data['qty_from'];
        $qtyTo = $data['qty_to'];
        $ruleId = $data['ruleId'];
        $ruleType = $data['rule_type'];
        $position = $data['position'];
        $connection = $this->resource->getConnection();

        $query = "insert into beecom_custom_stock_status_rule (text, qty_from, qty_to, rule_id, rule_type, position) values ('$text', $qtyFrom, $qtyTo, $ruleId, $ruleType, $position)";
        $connection->query($query);
    }
}
