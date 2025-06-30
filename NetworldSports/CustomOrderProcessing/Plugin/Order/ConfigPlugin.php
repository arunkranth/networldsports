<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Plugin\Order;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Config;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Config Plugin
 *
 * Adds your custom statuses into Magento's state‐status map.
 */
class ConfigPlugin
{
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private LoggerInterface $logger;
    /** @var array|null */
    private ?array $customStatuses = null;

    public function __construct(
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->searchCriteriaBuilder       = $searchCriteriaBuilder;
        $this->logger                      = $logger;
    }

    /**
     * @param Config   $subject
     * @param array    $result
     * @param mixed|null $state
     * @return array
     */
    public function afterGetStateStatuses(
        Config $subject,
        array $result,
        mixed $state = null
    ): array {
        try {
            $custom = $this->getCustomStatuses();
            return array_merge($result, $custom);
        } catch (\Throwable $e) {
            $this->logger->error(
                'Failed to load custom order statuses: ' . $e->getMessage()
            );
            return $result;
        }
    }

    /**
     * Load your statuses once from the repository
     *
     * @return string[]  [status_code => label]
     * @throws LocalizedException
     */
    private function getCustomStatuses(): array
    {
        if ($this->customStatuses === null) {
            $this->customStatuses = [];
            $sc = $this->searchCriteriaBuilder->create();
            $list = $this->customOrderStatusRepository->getList($sc);
            foreach ($list->getItems() as $status) {
                $code = $status->getStatusName();
                $this->customStatuses[$code] = $code;
            }
        }
        return $this->customStatuses;
    }
}
