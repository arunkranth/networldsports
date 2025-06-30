<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface;

/**
 * Order Status Log Repository interface - Read Only
 *
 * @api
 */
interface OrderStatusLogRepositoryInterface
{
    /**
     * Save order status log
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface $orderStatusLog
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(OrderStatusLogInterface $orderStatusLog): OrderStatusLogInterface;

    /**
     * Retrieve order status log by ID
     *
     * @param int $logId
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $logId): OrderStatusLogInterface;

    /**
     * Retrieve order status logs matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderStatusLogSearchResultsInterface;
}
