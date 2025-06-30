<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus as ResourceModel;

/**
 * Custom Order Status Model
 */
class CustomOrderStatus extends AbstractExtensibleModel implements CustomOrderStatusInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'networldsports_custom_order_status';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'networldsports_custom_order_status';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getStatusId(): ?int
    {
        return $this->getData(self::STATUS_ID) ? (int)$this->getData(self::STATUS_ID) : null;
    }

    /**
     * @inheritDoc
     */
    public function setStatusId(int $statusId): CustomOrderStatusInterface
    {
        return $this->setData(self::STATUS_ID, $statusId);
    }

    /**
     * @inheritDoc
     */
    public function getStatusName(): string
    {
        return (string)$this->getData(self::STATUS_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setStatusName(string $statusName): CustomOrderStatusInterface
    {
        return $this->setData(self::STATUS_NAME, $statusName);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabled(): bool
    {
        return (bool)$this->getData(self::IS_ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function setIsEnabled(bool $isEnabled): CustomOrderStatusInterface
    {
        return $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): CustomOrderStatusInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt): CustomOrderStatusInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusExtensionInterface $extensionAttributes
    ): CustomOrderStatusInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
