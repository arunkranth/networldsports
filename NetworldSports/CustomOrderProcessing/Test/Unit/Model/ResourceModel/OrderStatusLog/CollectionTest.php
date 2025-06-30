<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model\ResourceModel\OrderStatusLog;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use NetworldSports\CustomOrderProcessing\Model\OrderStatusLog;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Order Status Log Collection Test
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private Collection $collection;

    /**
     * @var EntityFactoryInterface|MockObject
     */
    private $entityFactoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var FetchStrategyInterface|MockObject
     */
    private $fetchStrategyMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * @var AbstractDb|MockObject
     */
    private $resourceMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->entityFactoryMock = $this->createMock(EntityFactoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->fetchStrategyMock = $this->createMock(FetchStrategyInterface::class);
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        $this->resourceMock = $this->createMock(AbstractDb::class);

        $this->resourceMock->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->collection = new Collection(
            $this->entityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->eventManagerMock,
            $this->connectionMock,
            $this->resourceMock
        );
    }

    /**
     * Test construct
     */
    public function testConstruct(): void
    {
        // Use reflection to check protected properties
        $reflection = new \ReflectionClass($this->collection);

        $idFieldProperty = $reflection->getProperty('_idFieldName');
        $idFieldProperty->setAccessible(true);
        $this->assertEquals('log_id', $idFieldProperty->getValue($this->collection));

        $eventPrefixProperty = $reflection->getProperty('_eventPrefix');
        $eventPrefixProperty->setAccessible(true);
        $this->assertEquals('networldsports_order_status_log_collection', $eventPrefixProperty->getValue($this->collection));

        $eventObjectProperty = $reflection->getProperty('_eventObject');
        $eventObjectProperty->setAccessible(true);
        $this->assertEquals('order_status_log_collection', $eventObjectProperty->getValue($this->collection));
    }

    /**
     * Test model and resource model initialization
     */
    public function testModelResourceInitialization(): void
    {
        $reflection = new \ReflectionClass($this->collection);
        $method = $reflection->getMethod('_construct');
        $method->setAccessible(true);

        $method->invoke($this->collection);

        $modelProperty = $reflection->getProperty('_model');
        $modelProperty->setAccessible(true);
        $this->assertEquals(OrderStatusLog::class, $modelProperty->getValue($this->collection));

        $resourceModelProperty = $reflection->getProperty('_resourceModel');
        $resourceModelProperty->setAccessible(true);
        $this->assertEquals(ResourceModel::class, $resourceModelProperty->getValue($this->collection));
    }
}
