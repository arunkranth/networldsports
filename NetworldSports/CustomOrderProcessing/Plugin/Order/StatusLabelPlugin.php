<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Plugin\Order;

use Magento\Sales\Model\Order;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Ensure $order->getStatusLabel() always returns your custom name when core is blank.
 */
class StatusLabelPlugin
{
    private CustomOrderStatusRepositoryInterface $repo;
    private LoggerInterface $logger;

    public function __construct(
        CustomOrderStatusRepositoryInterface $repo,
        LoggerInterface $logger
    ) {
        $this->repo   = $repo;
        $this->logger = $logger;
    }

    /**
     * Around getStatusLabel on the Order model.
     *
     * @param Order    $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetStatusLabel(Order $subject, callable $proceed): string
    {
        $label = (string)$proceed();
        $code  = (string)$subject->getStatus();
        if ($code && ($label === '' || $label === $code)) {
            try {
                $custom = $this->repo->getByName($code);
                return (string)$custom->getStatusName();
            } catch (\Exception $e) {
                $this->logger->debug(
                    "Custom status lookup failed for '{$code}': " . $e->getMessage()
                );
            }
        }
        return $label;
    }
}
