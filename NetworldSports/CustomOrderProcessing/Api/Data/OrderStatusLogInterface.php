<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Order Status Log Interface
 *
 * @api
 */
interface OrderStatusLogInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array
     */
    const LOG_ID = 'log_id';
    const ORDER_ID = 'order_id';
    const OLD_STATUS = 'old_status';
    const NEW_STATUS = 'new_status';
    const CHANGED_AT = 'changed_at';

    /**
     * Get log ID
     *
     * @return int|null
     */
    public function getLogId(): ?int;

    /**
     * Set log ID
     *
     * @param int $logId
     * @return $this
     */
    public function setLogId(int $logId): self;

    /**
     * Get order ID
     *
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId(int $orderId): self;

    /**
     * Get old status
     *
     * @return string|null
     */
    public function getOldStatus(): ?string;

    /**
     * Set old status
     *
     * @param string|null $oldStatus
     * @return $this
     */
    public function setOldStatus(?string $oldStatus): self;

    /**
     * Get new status
     *
     * @return string
     */
    public function getNewStatus(): string;

    /**
     * Set new status
     *
     * @param string $newStatus
     * @return $this
     */
    public function setNewStatus(string $newStatus): self;

    /**
     * Get changed at
     *
     * @return string|null
     */
    public function getChangedAt(): ?string;

    /**
     * Set changed at
     *
     * @param string $changedAt
     * @return $this
     */
    public function setChangedAt(string $changedAt): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogExtensionInterface $extensionAttributes
    ): self;
}
