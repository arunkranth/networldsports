<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Custom Order Status Interface
 *
 * @api
 */
interface CustomOrderStatusInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array
     */
    const STATUS_ID = 'status_id';
    const STATUS_NAME = 'status_name';
    const IS_ENABLED = 'is_enabled';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get status ID
     *
     * @return int|null
     */
    public function getStatusId(): ?int;

    /**
     * Set status ID
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId(int $statusId): self;

    /**
     * Get status name
     *
     * @return string
     */
    public function getStatusName(): string;

    /**
     * Set status name
     *
     * @param string $statusName
     * @return $this
     */
    public function setStatusName(string $statusName): self;

    /**
     * Get is enabled
     *
     * @return bool
     */
    public function getIsEnabled(): bool;

    /**
     * Set is enabled
     *
     * @param bool $isEnabled
     * @return $this
     */
    public function setIsEnabled(bool $isEnabled): self;

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusExtensionInterface $extensionAttributes
    ): self;
}
