<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for order status log search results
 *
 * @api
 */
interface OrderStatusLogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get order status log list
     *
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface[]
     */
    public function getItems();

    /**
     * Set order status log list
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
