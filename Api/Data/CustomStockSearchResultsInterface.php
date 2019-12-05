<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beecom\CustomStockStatus\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for cms page search results.
 * @api
 * @since 100.0.2
 */
interface CustomStockSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return \Beecom\CustomStockStatus\Api\Data\CustomStockInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param \Beecom\CustomStockStatus\Api\Data\CustomStockInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
