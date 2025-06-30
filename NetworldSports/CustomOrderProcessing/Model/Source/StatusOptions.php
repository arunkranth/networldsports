<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Status Options source model
 */
class StatusOptions implements OptionSourceInterface
{
    /**
     * @var OrderConfig
     */
    private OrderConfig $orderConfig;

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @param OrderConfig $orderConfig
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderConfig $orderConfig,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->orderConfig = $orderConfig;
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];

            // Add core statuses
            foreach ($this->orderConfig->getStatuses() as $code => $label) {
                $this->options[] = [
                    'value' => $code,
                    'label' => $label
                ];
            }

            // Add custom statuses
            try {
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $customStatuses = $this->customOrderStatusRepository->getList($searchCriteria);

                foreach ($customStatuses->getItems() as $status) {
                    $this->options[] = [
                        'value' => $status->getStatusName(),
                        'label' => $status->getStatusName()
                    ];
                }
            } catch (\Exception $e) {
                $this->logger->error('Error loading custom statuses: ' . $e->getMessage());
            }
        }

        return $this->options;
    }
}
