<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus as ResourceModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Custom Order Status Resource Model Test
 */
class CustomOrderStatusTest extends TestCase
{
    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var OrderCollectionFactory|MockObject
     */
    private $orderCollectionFactoryMock;

    /**
     * @var OrderConfig|MockObject
     */
    private $orderConfigMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->orderCollectionFactoryMock = $this->createMock(OrderCollectionFactory::class);
        $this->orderConfigMock = $this->createMock(OrderConfig::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->contextMock->expects($this->any())
            ->method('getResources')
            ->willReturn($this->resourceConnectionMock);

        $this->resourceConnectionMock->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->resourceModel = new ResourceModel(
            $this->contextMock,
            $this->orderCollectionFactoryMock,
            $this->orderConfigMock
        );
    }

    /**
     * Test before delete
     */
    public function testBeforeDelete(): void
    {
        $statusName = 'Custom Shipped';
        $defaultStatus = 'processing';
        $state = 'processing';

        $modelMock = $this->createMock(CustomOrderStatus::class);
        $modelMock->expects($this->once())
            ->method('getStatusName')
            ->willReturn($statusName);

        $orderCollectionMock = $this->createMock(Collection::class);
        $this->orderCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderCollectionMock);

        $orderCollectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('status', $statusName)
            ->willReturnSelf();

        $orderMock1 = $this->createMock(Order::class);
        $orderMock2 = $this->createMock(Order::class);

        $orderCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$orderMock1, $orderMock2]));

        $orderMock1->expects($this->once())
            ->method('getState')
            ->willReturn($state);
        $orderMock1->expects($this->once())
            ->method('setStatus')
            ->with($defaultStatus);
        $orderMock1->expects($this->once())
            ->method('save');

        $orderMock2->expects($this->once())
            ->method('getState')
            ->willReturn($state);
        $orderMock2->expects($this->once())
            ->method('setStatus')
            ->with($defaultStatus);
        $orderMock2->expects($this->once())
            ->method('save');

        $this->orderConfigMock->expects($this->exactly(2))
            ->method('getStateDefaultStatus')
            ->with($state)
            ->willReturn($defaultStatus);

        // Use reflection to test protected method
        $reflection = new \ReflectionClass($this->resourceModel);
        $method = $reflection->getMethod('_beforeDelete');
        $method->setAccessible(true);

        $result = $method->invoke($this->resourceModel, $modelMock);
        $this->assertSame($this->resourceModel, $result);
    }
}
