<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatusRepository;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus as ResourceModel;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus\Collection;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Custom Order Status Repository Test
 */
class CustomOrderStatusRepositoryTest extends TestCase
{
    /**
     * @var CustomOrderStatusRepository
     */
    private CustomOrderStatusRepository $repository;

    /**
     * @var ResourceModel|MockObject
     */
    private $resourceMock;

    /**
     * @var CustomOrderStatusInterfaceFactory|MockObject
     */
    private $customOrderStatusFactoryMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var CustomOrderStatusSearchResultsInterfaceFactory|MockObject
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
        $this->customOrderStatusFactoryMock = $this->createMock(CustomOrderStatusInterfaceFactory::class);
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->searchResultsFactoryMock = $this->createMock(CustomOrderStatusSearchResultsInterfaceFactory::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->repository = new CustomOrderStatusRepository(
            $this->resourceMock,
            $this->customOrderStatusFactoryMock,
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
        $customOrderStatusMock = $this->createMock(CustomOrderStatusInterface::class);

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($customOrderStatusMock);

        $result = $this->repository->save($customOrderStatusMock);
        $this->assertSame($customOrderStatusMock, $result);
    }

    /**
     * Test save with exception
     */
    public function testSaveWithException(): void
    {
        $customOrderStatusMock = $this->createMock(CustomOrderStatusInterface::class);
        $exception = new \Exception('Save error');

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($customOrderStatusMock)
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($exception->getMessage());

        $this->expectException(CouldNotDeleteException::class);
        $this->repository->delete($customOrderStatusMock);
    }

    /**
     * Test get list
     */
    public function testGetList(): void
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $collectionMock = $this->createMock(Collection::class);
        $searchResultsMock = $this->createMock(CustomOrderStatusSearchResultsInterface::class);

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
            $this->createMock(CustomOrderStatusInterface::class),
            $this->createMock(CustomOrderStatusInterface::class)
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
