<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for custom order status search results
 *
 * @api
 */
interface CustomOrderStatusSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get custom order status list
     *
     * @return CustomOrderStatusInterface[]
     */
    public function getItems();

    /**
     * Set custom order status list
     *
     * @param CustomOrderStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
