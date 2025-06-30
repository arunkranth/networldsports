<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model;

use NetworldSports\CustomOrderProcessing\Model\OrderStatusLog;
use PHPUnit\Framework\TestCase;

/**
 * Order Status Log Test
 */
class OrderStatusLogTest extends TestCase
{
    /**
     * @var OrderStatusLog
     */
    private OrderStatusLog $model;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->model = $this->getMockBuilder(OrderStatusLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['_init'])
            ->getMock();
    }

    /**
     * Test log id getter and setter
     */
    public function testLogId(): void
    {
        $value = 123;
        $this->model->setLogId($value);
        $this->assertEquals($value, $this->model->getLogId());
    }

    /**
     * Test order id getter and setter
     */
    public function testOrderId(): void
    {
        $value = 456;
        $this->model->setOrderId($value);
        $this->assertEquals($value, $this->model->getOrderId());
    }

    /**
     * Test old status getter and setter
     */
    public function testOldStatus(): void
    {
        $value = 'pending';
        $this->model->setOldStatus($value);
        $this->assertEquals($value, $this->model->getOldStatus());

        // Test null value
        $this->model->setOldStatus(null);
        $this->assertNull($this->model->getOldStatus());
    }

    /**
     * Test new status getter and setter
     */
    public function testNewStatus(): void
    {
        $value = 'processing';
        $this->model->setNewStatus($value);
        $this->assertEquals($value, $this->model->getNewStatus());
    }

    /**
     * Test changed at getter and setter
     */
    public function testChangedAt(): void
    {
        $value = '2024-01-01 00:00:00';
        $this->model->setChangedAt($value);
        $this->assertEquals($value, $this->model->getChangedAt());
    }
}
