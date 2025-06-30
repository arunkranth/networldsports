<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model\Api;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Api\StatusManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Status Management Service
 */
class StatusManagement implements StatusManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var OrderConfig
     */
    private OrderConfig $orderConfig;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param OrderConfig $orderConfig
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        OrderConfig $orderConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->orderConfig = $orderConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function updateOrderStatus(string $orderIncrementId, string $newStatus): array
    {
        try {
            // Validate input
            if (empty($orderIncrementId)) {
                throw new InputException(__('Order increment ID is required.'));
            }
            if (empty($newStatus)) {
                throw new InputException(__('New status is required.'));
            }

            // Load order
            $order = $this->loadOrderByIncrementId($orderIncrementId);

            // Validate status exists and is enabled
            $this->validateStatus($newStatus);

            // Validate transition is allowed
            $currentState = $order->getState();
            if (!$this->isTransitionAllowed($currentState, $newStatus)) {
                throw new StateException(
                    __('Status transition from "%1" to "%2" is not allowed.', $order->getStatus(), $newStatus)
                );
            }

            // Update order status
            $order->setStatus($newStatus);
            $this->orderRepository->save($order);

            return [
                'success' => true,
                'message' => __('Order status updated successfully.')->render(),
                'order_id' => $order->getIncrementId(),
                'new_status' => $newStatus
            ];

        } catch (\Exception $e) {
            $this->logger->error('Failed to update order status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Load order by increment ID
     *
     * @param string $incrementId
     * @return Order
     * @throws NoSuchEntityException
     */
    private function loadOrderByIncrementId(string $incrementId): Order
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        if (empty($orders)) {
            throw new NoSuchEntityException(__('Order with increment ID "%1" does not exist.', $incrementId));
        }

        return reset($orders);
    }

    /**
     * Validate if status exists and is enabled
     *
     * @param string $statusName
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function validateStatus(string $statusName): void
    {
        // Check if it's a core status
        $coreStatuses = $this->orderConfig->getStatuses();
        if (isset($coreStatuses[$statusName])) {
            return;
        }

        // Check if it's a custom status
        try {
            $customStatus = $this->customOrderStatusRepository->getByName($statusName);
            if (!$customStatus->getIsEnabled()) {
                throw new LocalizedException(__('Status "%1" is disabled.', $statusName));
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Status "%1" does not exist.', $statusName));
        }
    }

    /**
     * Check if status transition is allowed
     *
     * @param string $currentState
     * @param string $newStatus
     * @return bool
     */
    private function isTransitionAllowed(string $currentState, string $newStatus): bool
    {
        $stateStatuses = $this->orderConfig->getStateStatuses($currentState);
        return isset($stateStatuses[$newStatus]);
    }
}
