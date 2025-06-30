<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\Api\SearchResults;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface;

/**
 * Order Status Log Search Results
 */
class OrderStatusLogSearchResults extends SearchResults implements OrderStatusLogSearchResultsInterface
{
}
