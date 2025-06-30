<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\OrderStatusLogRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Order Status Log Repository - Read Only Implementation
 */
class OrderStatusLogRepository implements OrderStatusLogRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private ResourceModel $resource;

    /**
     * @var OrderStatusLogInterfaceFactory
     */
    private OrderStatusLogInterfaceFactory $orderStatusLogFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var OrderStatusLogSearchResultsInterfaceFactory
     */
    private OrderStatusLogSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ResourceModel $resource
     * @param OrderStatusLogInterfaceFactory $orderStatusLogFactory
     * @param CollectionFactory $collectionFactory
     * @param OrderStatusLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel $resource,
        OrderStatusLogInterfaceFactory $orderStatusLogFactory,
        CollectionFactory $collectionFactory,
        OrderStatusLogSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->orderStatusLogFactory = $orderStatusLogFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function save(OrderStatusLogInterface $orderStatusLog): OrderStatusLogInterface
    {
        try {
            $this->resource->save($orderStatusLog);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new CouldNotSaveException(
                __('Could not save the order status log: %1', $exception->getMessage()),
                $exception
            );
        }
        return $orderStatusLog;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $logId): OrderStatusLogInterface
    {
        $orderStatusLog = $this->orderStatusLogFactory->create();
        $this->resource->load($orderStatusLog, $logId);
        if (!$orderStatusLog->getId()) {
            throw new NoSuchEntityException(__('Order status log with id "%1" does not exist.', $logId));
        }
        return $orderStatusLog;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderStatusLogSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var OrderStatusLogSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
