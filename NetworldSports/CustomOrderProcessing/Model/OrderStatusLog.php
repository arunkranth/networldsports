<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;

/**
 * Order Status Log Model
 */
class OrderStatusLog extends AbstractExtensibleModel implements OrderStatusLogInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'networldsports_order_status_log';

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
     * @inheritDoc
     */
    public function getLogId(): ?int
    {
        return $this->getData(self::LOG_ID) ? (int)$this->getData(self::LOG_ID) : null;
    }

    /**
     * @inheritDoc
     */
    public function setLogId(int $logId): OrderStatusLogInterface
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $orderId): OrderStatusLogInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getOldStatus(): ?string
    {
        return $this->getData(self::OLD_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setOldStatus(?string $oldStatus): OrderStatusLogInterface
    {
        return $this->setData(self::OLD_STATUS, $oldStatus);
    }

    /**
     * @inheritDoc
     */
    public function getNewStatus(): string
    {
        return (string)$this->getData(self::NEW_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setNewStatus(string $newStatus): OrderStatusLogInterface
    {
        return $this->setData(self::NEW_STATUS, $newStatus);
    }

    /**
     * @inheritDoc
     */
    public function getChangedAt(): ?string
    {
        return $this->getData(self::CHANGED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setChangedAt(string $changedAt): OrderStatusLogInterface
    {
        return $this->setData(self::CHANGED_AT, $changedAt);
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
        \NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogExtensionInterface $extensionAttributes
    ): OrderStatusLogInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
