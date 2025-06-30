<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus as ResourceModel;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Custom Order Status Repository
 */
class CustomOrderStatusRepository implements CustomOrderStatusRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private ResourceModel $resource;

    /**
     * @var CustomOrderStatusInterfaceFactory
     */
    private CustomOrderStatusInterfaceFactory $customOrderStatusFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CustomOrderStatusSearchResultsInterfaceFactory
     */
    private CustomOrderStatusSearchResultsInterfaceFactory $searchResultsFactory;

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
     * @param CustomOrderStatusInterfaceFactory $customOrderStatusFactory
     * @param CollectionFactory $collectionFactory
     * @param CustomOrderStatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel $resource,
        CustomOrderStatusInterfaceFactory $customOrderStatusFactory,
        CollectionFactory $collectionFactory,
        CustomOrderStatusSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->customOrderStatusFactory = $customOrderStatusFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function save(CustomOrderStatusInterface $customOrderStatus): CustomOrderStatusInterface
    {
        try {
            $this->resource->save($customOrderStatus);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new CouldNotSaveException(
                __('Could not save the custom order status: %1', $exception->getMessage()),
                $exception
            );
        }
        return $customOrderStatus;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $statusId): CustomOrderStatusInterface
    {
        $customOrderStatus = $this->customOrderStatusFactory->create();
        $this->resource->load($customOrderStatus, $statusId);
        if (!$customOrderStatus->getId()) {
            throw new NoSuchEntityException(__('Custom order status with id "%1" does not exist.', $statusId));
        }
        return $customOrderStatus;
    }

    /**
     * @inheritDoc
     */
    public function getByName(string $statusName): CustomOrderStatusInterface
    {
        $customOrderStatus = $this->customOrderStatusFactory->create();
        $this->resource->load($customOrderStatus, $statusName, 'status_name');
        if (!$customOrderStatus->getId()) {
            throw new NoSuchEntityException(__('Custom order status with name "%1" does not exist.', $statusName));
        }
        return $customOrderStatus;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CustomOrderStatusSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(CustomOrderStatusInterface $customOrderStatus): bool
    {
        try {
            $this->resource->delete($customOrderStatus);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new CouldNotDeleteException(
                __('Could not delete the custom order status: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $statusId): bool
    {
        return $this->delete($this->getById($statusId));
    }
}
