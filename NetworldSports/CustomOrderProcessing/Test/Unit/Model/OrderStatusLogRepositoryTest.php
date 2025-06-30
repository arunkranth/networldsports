<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Model\OrderStatusLog;
use NetworldSports\CustomOrderProcessing\Model\OrderStatusLogRepository;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\Collection;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Order Status Log Repository Test
 */
class OrderStatusLogRepositoryTest extends TestCase
{
    /**
     * @var OrderStatusLogRepository
     */
    private OrderStatusLogRepository $repository;

    /**
     * @var ResourceModel|MockObject
     */
    private $resourceMock;

    /**
     * @var OrderStatusLogInterfaceFactory|MockObject
     */
    private $orderStatusLogFactoryMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var OrderStatusLogSearchResultsInterfaceFactory|MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var CollectionProcessorInterface|MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->resourceMock = $this->createMock(ResourceModel::class);
        $this->orderStatusLogFactoryMock = $this->createMock(OrderStatusLogInterfaceFactory::class);
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->searchResultsFactoryMock = $this->createMock(OrderStatusLogSearchResultsInterfaceFactory::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->repository = new OrderStatusLogRepository(
            $this->resourceMock,
            $this->orderStatusLogFactoryMock,
            $this->collectionFactoryMock,
            $this->searchResultsFactoryMock,
            $this->collectionProcessorMock,
            $this->loggerMock
        );
    }

    /**
     * Test save
     */
    public function testSave(): void
    {
        $orderStatusLogMock = $this->createMock(OrderStatusLogInterface::class);

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($orderStatusLogMock);

        $result = $this->repository->save($orderStatusLogMock);
        $this->assertSame($orderStatusLogMock, $result);
    }

    /**
     * Test save with exception
     */
    public function testSaveWithException(): void
    {
        $orderStatusLogMock = $this->createMock(OrderStatusLogInterface::class);
        $exception = new \Exception('Save error');

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($orderStatusLogMock)
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($exception->getMessage());

        $this->expectException(CouldNotSaveException::class);
        $this->repository->save($orderStatusLogMock);
    }

    /**
     * Test get by id
     */
    public function testGetById(): void
    {
        $logId = 1;
        $orderStatusLogMock = $this->createMock(OrderStatusLog::class);

        $this->orderStatusLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderStatusLogMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($orderStatusLogMock, $logId);

        $orderStatusLogMock->expects($this->once())
            ->method('getId')
            ->willReturn($logId);

        $result = $this->repository->getById($logId);
        $this->assertSame($orderStatusLogMock, $result);
    }

    /**
     * Test get list
     */
    public function testGetList(): void
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $collectionMock = $this->createMock(Collection::class);
        $searchResultsMock = $this->createMock(OrderStatusLogSearchResultsInterface::class);

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $items = [
            $this->createMock(OrderStatusLogInterface::class),
            $this->createMock(OrderStatusLogInterface::class)
        ];

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $collectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(2);

        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($items);

        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with(2);

        $result = $this->repository->getList($searchCriteriaMock);
        $this->assertSame($searchResultsMock, $result);
    }
}
