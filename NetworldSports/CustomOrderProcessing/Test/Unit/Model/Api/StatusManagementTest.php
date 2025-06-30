<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model\Api;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Model\Api\StatusManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Status Management Test
 */
class StatusManagementTest extends TestCase
{
    /**
     * @var StatusManagement
     */
    private StatusManagement $statusManagement;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var CustomOrderStatusRepositoryInterface|MockObject
     */
    private $customOrderStatusRepositoryMock;

    /**
     * @var OrderConfig|MockObject
     */
    private $orderConfigMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->customOrderStatusRepositoryMock = $this->createMock(CustomOrderStatusRepositoryInterface::class);
        $this->orderConfigMock = $this->createMock(OrderConfig::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->statusManagement = new StatusManagement(
            $this->orderRepositoryMock,
            $this->customOrderStatusRepositoryMock,
            $this->orderConfigMock,
            $this->searchCriteriaBuilderMock,
            $this->loggerMock
        );
    }

    /**
     * Test update order status with empty increment ID
     */
    public function testUpdateOrderStatusEmptyIncrementId(): void
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Order increment ID is required.');

        $this->statusManagement->updateOrderStatus('', 'processing');
    }

    /**
     * Test update order status with empty new status
     */
    public function testUpdateOrderStatusEmptyNewStatus(): void
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('New status is required.');

        $this->statusManagement->updateOrderStatus('000000001', '');
    }

    /**
     * Test update order status with order not found
     */
    public function testUpdateOrderStatusOrderNotFound(): void
    {
        $incrementId = '000000001';
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultMock = $this->createMock(OrderSearchResultInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Order with increment ID "000000001" does not exist.');

        $this->statusManagement->updateOrderStatus($incrementId, 'processing');
    }

    /**
     * Test update order status with core status
     */
    public function testUpdateOrderStatusWithCoreStatus(): void
    {
        $incrementId = '000000001';
        $newStatus = 'processing';
        $currentState = 'new';

        $orderMock = $this->createMock(Order::class);
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultMock = $this->createMock(OrderSearchResultInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$orderMock]);

        // Core status check
        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn(['processing' => 'Processing']);

        $orderMock->expects($this->once())
            ->method('getState')
            ->willReturn($currentState);

        $this->orderConfigMock->expects($this->once())
            ->method('getStateStatuses')
            ->with($currentState)
            ->willReturn(['processing' => 'Processing']);

        $orderMock->expects($this->once())
            ->method('setStatus')
            ->with($newStatus);

        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($orderMock);

        $orderMock->expects($this->any())
            ->method('getStatus')
            ->willReturn('pending');

        $orderMock->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $result = $this->statusManagement->updateOrderStatus($incrementId, $newStatus);

        $this->assertTrue($result['success']);
        $this->assertEquals('Order status updated successfully.', $result['message']);
        $this->assertEquals($incrementId, $result['order_id']);
        $this->assertEquals($newStatus, $result['new_status']);
    }

    /**
     * Test update order status with custom status
     */
    public function testUpdateOrderStatusWithCustomStatus(): void
    {
        $incrementId = '000000001';
        $newStatus = 'Custom Shipped';
        $currentState = 'processing';

        $orderMock = $this->createMock(Order::class);
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultMock = $this->createMock(OrderSearchResultInterface::class);
        $customStatusMock = $this->createMock(CustomOrderStatusInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$orderMock]);

        // Not a core status
        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn(['processing' => 'Processing']);

        // Check custom status
        $this->customOrderStatusRepositoryMock->expects($this->once())
            ->method('getByName')
            ->with($newStatus)
            ->willReturn($customStatusMock);

        $customStatusMock->expects($this->once())
            ->method('getIsEnabled')
            ->willReturn(true);

        $orderMock->expects($this->once())
            ->method('getState')
            ->willReturn($currentState);

        $this->orderConfigMock->expects($this->once())
            ->method('getStateStatuses')
            ->with($currentState)
            ->willReturn(['Custom Shipped' => 'Custom Shipped']);

        $orderMock->expects($this->once())
            ->method('setStatus')
            ->with($newStatus);

        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($orderMock);

        $orderMock->expects($this->any())
            ->method('getStatus')
            ->willReturn('processing');

        $orderMock->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $result = $this->statusManagement->updateOrderStatus($incrementId, $newStatus);

        $this->assertTrue($result['success']);
    }

    /**
     * Test update order status with disabled custom status
     */
    public function testUpdateOrderStatusWithDisabledCustomStatus(): void
    {
        $incrementId = '000000001';
        $newStatus = 'Custom Shipped';

        $orderMock = $this->createMock(Order::class);
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultMock = $this->createMock(OrderSearchResultInterface::class);
        $customStatusMock = $this->createMock(CustomOrderStatusInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$orderMock]);

        // Not a core status
        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn(['processing' => 'Processing']);

        // Check custom status
        $this->customOrderStatusRepositoryMock->expects($this->once())
            ->method('getByName')
            ->with($newStatus)
            ->willReturn($customStatusMock);

        $customStatusMock->expects($this->once())
            ->method('getIsEnabled')
            ->willReturn(false);

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Status "Custom Shipped" is disabled.');

        $this->statusManagement->updateOrderStatus($incrementId, $newStatus);
    }

    /**
     * Test update order status with invalid transition
     */
    public function testUpdateOrderStatusWithInvalidTransition(): void
    {
        $incrementId = '000000001';
        $newStatus = 'processing';
        $currentState = 'complete';

        $orderMock = $this->createMock(Order::class);
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultMock = $this->createMock(OrderSearchResultInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$orderMock]);

        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn(['processing' => 'Processing']);

        $orderMock->expects($this->once())
            ->method('getState')
            ->willReturn($currentState);

        $orderMock->expects($this->once())
            ->method('getStatus')
            ->willReturn('complete');

        $this->orderConfigMock->expects($this->once())
            ->method('getStateStatuses')
            ->with($currentState)
            ->willReturn(['complete' => 'Complete']); // processing not allowed

        $this->expectException(StateException::class);

        $this->statusManagement->updateOrderStatus($incrementId, $newStatus);
    }
}
