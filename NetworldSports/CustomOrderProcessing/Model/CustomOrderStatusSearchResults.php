<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\Api\SearchResults;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface;

/**
 * Custom Order Status Search Results
 */
class CustomOrderStatusSearchResults extends SearchResults implements CustomOrderStatusSearchResultsInterface
{
}
