<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Order\Config as OrderConfig;

/**
 * Custom Order Status Resource Model
 */
class CustomOrderStatus extends AbstractDb
{
    /**
     * @var OrderCollectionFactory
     */
    private OrderCollectionFactory $orderCollectionFactory;

    /**
     * @var OrderConfig
     */
    private OrderConfig $orderConfig;

    /**
     * @param Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderConfig $orderConfig
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory,
        OrderConfig $orderConfig,
        ?string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderConfig = $orderConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('networldsports_custom_order_status', 'status_id');
    }

    /**
     * Process data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        // Fallback orders to core status before deletion
        $this->fallbackOrdersToDefaultStatus($object->getStatusName());
        return parent::_beforeDelete($object);
    }

    /**
     * Fallback orders with custom status to default core status
     *
     * @param string $customStatusName
     * @return void
     */
    private function fallbackOrdersToDefaultStatus(string $customStatusName): void
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('status', $customStatusName);

        foreach ($orderCollection as $order) {
            $state = $order->getState();
            $defaultStatus = $this->orderConfig->getStateDefaultStatus($state);
            if ($defaultStatus) {
                $order->setStatus($defaultStatus);
                $order->save();
            }
        }
    }
}
