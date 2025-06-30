<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Plugin\Sales;

use Magento\Sales\Model\Order;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Plugin
 */
class OrderPlugin
{
    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        LoggerInterface $logger
    ) {
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->logger = $logger;
    }

    /**
     * Get proper status label for custom statuses
     *
     * @param Order $subject
     * @param string|null $result
     * @return string|null
     */
    public function afterGetStatusLabel(Order $subject, ?string $result): ?string
    {
        try {
            $status = $subject->getStatus();

            if ($status && (empty($result) || $result === $status)) {
                try {
                    $customStatus = $this->customOrderStatusRepository->getByName($status);
                    return $customStatus->getStatusName();
                } catch (\Exception $e) {
                    $this->logger->error('Not a custom status, return original result: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error getting custom status label: ' . $e->getMessage());
        }

        return $result;
    }
}
