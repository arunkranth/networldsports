<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Plugin\Sales;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Repository Plugin
 */
class OrderRepositoryPlugin
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
     * Filter out orders with disabled custom statuses
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $result
     * @param SearchCriteriaInterface $searchCriteria
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $result,
        SearchCriteriaInterface $searchCriteria
    ): OrderSearchResultInterface {
        try {
            $items = $result->getItems();
            $filteredItems = [];

            foreach ($items as $order) {
                $status = $order->getStatus();

                // Check if it's a custom status
                if ($this->isCustomStatus($status)) {
                    try {
                        $customStatus = $this->customOrderStatusRepository->getByName($status);
                        // Only include if enabled
                        if ($customStatus->getIsEnabled()) {
                            $filteredItems[] = $order;
                        }
                    } catch (\Exception $e) {
                        // Custom status not found, skip order
                        continue;
                    }
                } else {
                    // Core status, always include
                    $filteredItems[] = $order;
                }
            }

            $result->setItems($filteredItems);
            $result->setTotalCount(count($filteredItems));

        } catch (\Exception $e) {
            $this->logger->error('Error filtering orders by custom status: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Check if status is custom (not core)
     *
     * @param string $statusName
     * @return bool
     */
    private function isCustomStatus(string $statusName): bool
    {
        $coreStatuses = $this->orderConfig->getStatuses();
        return !isset($coreStatuses[$statusName]);
    }
}
