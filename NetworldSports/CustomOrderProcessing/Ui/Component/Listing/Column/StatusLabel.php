<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Ui\Component\Listing\Columns\Column;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Status Label Column
 */
class StatusLabel extends Column
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderConfig $orderConfig
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param LoggerInterface $logger
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderConfig $orderConfig,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        LoggerInterface $logger,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderConfig = $orderConfig;
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->logger = $logger;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    $statusValue = $item[$fieldName];

                    // First check core statuses
                    $coreStatuses = $this->orderConfig->getStatuses();
                    if (isset($coreStatuses[$statusValue])) {
                        $item[$fieldName] = $coreStatuses[$statusValue];
                        continue;
                    }

                    // Then check custom statuses (including disabled ones for historical data)
                    try {
                        $customStatus = $this->customOrderStatusRepository->getByName($statusValue);
                        $item[$fieldName] = $customStatus->getStatusName();
                    } catch (\Exception $e) {
                        // Status not found, show the raw value
                        $item[$fieldName] = $statusValue ?: __('Unknown');
                    }
                }
            }
        }

        return $dataSource;
    }
}
