<?php

namespace Beecom\CustomStockStatus\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * CMS label CRUD interface.
 * @api
 * @since 100.0.2
 */
interface RuleRepositoryInterface
{
    /**
     * Save label.
     *
     * @param \Beecom\CustomStockStatus\Api\Data\RuleInterface $label
     * @return \Beecom\CustomStockStatus\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\RuleInterface $label);

    /**
     * Retrieve label.
     *
     * @param int $labelId
     * @return \Beecom\CustomStockStatus\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($labelId);

    /**
     * Delete label.
     *
     * @param \Beecom\CustomStockStatus\Api\Data\RuleInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\RuleInterface $label);

    /**
     * Delete label by ID.
     *
     * @param int $labelId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($labelId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Beecom\CustomStockStatus\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
