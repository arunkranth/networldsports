<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api;

/**
 * Interface for managing order status updates via API
 *
 * @api
 */
interface StatusManagementInterface
{
    /**
     * Update order status
     *
     * @param string $orderIncrementId
     * @param string $newStatus
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateOrderStatus(string $orderIncrementId, string $newStatus): array;
}
