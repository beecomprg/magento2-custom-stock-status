<?php

namespace Beecom\CustomStockStatus\Api;

/**
 * CMS label CRUD interface.
 * @api
 * @since 100.0.2
 */
interface CustomStockRepositoryInterface
{
    /**
     * Save label.
     *
     * @param \Beecom\CustomStockStatus\Api\Data\CustomStockInterface $label
     * @return \Beecom\CustomStockStatus\Api\Data\CustomStockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\CustomStockInterface $label);

    /**
     * Retrieve label.
     *
     * @param int $labelId
     * @return \Beecom\CustomStockStatus\Api\Data\CustomStockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($labelId);

    /**
     * Delete label.
     *
     * @param \Beecom\CustomStockStatus\Api\Data\CustomStockInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\CustomStockInterface $label);

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
     * @return \Beecom\CustomStockStatus\Api\Data\CustomStockSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
