<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\ViewModel\Order;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Status View Model
 */
class StatusViewModel implements ArgumentInterface
{
    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var OrderConfig
     */
    private OrderConfig $orderConfig;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array
     */
    private array $customStatusCache = [];

    /**
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param OrderConfig $orderConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        OrderConfig $orderConfig,
        LoggerInterface $logger
    ) {
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->orderConfig = $orderConfig;
        $this->logger = $logger;
    }

    /**
     * Check if order should be shown
     *
     * @param Order $order
     * @return bool
     */
    public function shouldShowOrder(Order $order): bool
    {
        try {
            $status = $order->getStatus();

            // Check if it's a core status
            $coreStatuses = $this->orderConfig->getStatuses();
            if (isset($coreStatuses[$status])) {
                return true;
            }

            // Check if it's a custom status
            if (!isset($this->customStatusCache[$status])) {
                try {
                    $customStatus = $this->customOrderStatusRepository->getByName($status);
                    $this->customStatusCache[$status] = $customStatus->getIsEnabled();
                } catch (\Exception $e) {
                    $this->customStatusCache[$status] = false;
                }
            }

            return $this->customStatusCache[$status];
        } catch (\Exception $e) {
            $this->logger->error('Error checking order visibility: ' . $e->getMessage());
            return true; // Show order by default if error occurs
        }
    }

    /**
     * Get order status label
     *
     * @param Order $order
     * @return string
     */
    public function getOrderStatusLabel(Order $order): string
    {
        try {
            $status = $order->getStatus();

            // Check core statuses first
            $coreStatuses = $this->orderConfig->getStatuses();
            if (isset($coreStatuses[$status])) {
                return $coreStatuses[$status];
            }

            // Check custom statuses
            try {
                $customStatus = $this->customOrderStatusRepository->getByName($status);
                return $customStatus->getStatusName();
            } catch (\Exception $e) {
                return $status; // Return status name if not found
            }
        } catch (\Exception $e) {
            $this->logger->error('Error getting order status label: ' . $e->getMessage());
            return $order->getStatus();
        }
    }
}
