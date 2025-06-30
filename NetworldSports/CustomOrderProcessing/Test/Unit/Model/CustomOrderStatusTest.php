<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model;

use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;
use PHPUnit\Framework\TestCase;

/**
 * Custom Order Status Test
 */
class CustomOrderStatusTest extends TestCase
{
    /**
     * @var CustomOrderStatus
     */
    private CustomOrderStatus $model;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->model = $this->getMockBuilder(CustomOrderStatus::class)
            ->disableOriginalConstructor()
            ->setMethods(['_init'])
            ->getMock();
    }

    /**
     * Test get identities
     */
    public function testGetIdentities(): void
    {
        $id = 1;
        $this->model->setData('status_id', $id);
        $expected = [CustomOrderStatus::CACHE_TAG . '_' . $id];
        $this->assertEquals($expected, $this->model->getIdentities());
    }

    /**
     * Test status id getter and setter
     */
    public function testStatusId(): void
    {
        $value = 123;
        $this->model->setStatusId($value);
        $this->assertEquals($value, $this->model->getStatusId());
    }

    /**
     * Test status name getter and setter
     */
    public function testStatusName(): void
    {
        $value = 'Custom Shipped';
        $this->model->setStatusName($value);
        $this->assertEquals($value, $this->model->getStatusName());
    }

    /**
     * Test is enabled getter and setter
     */
    public function testIsEnabled(): void
    {
        $this->model->setIsEnabled(true);
        $this->assertTrue($this->model->getIsEnabled());

        $this->model->setIsEnabled(false);
        $this->assertFalse($this->model->getIsEnabled());
    }

    /**
     * Test created at getter and setter
     */
    public function testCreatedAt(): void
    {
        $value = '2024-01-01 00:00:00';
        $this->model->setCreatedAt($value);
        $this->assertEquals($value, $this->model->getCreatedAt());
    }

    /**
     * Test updated at getter and setter
     */
    public function testUpdatedAt(): void
    {
        $value = '2024-01-01 00:00:00';
        $this->model->setUpdatedAt($value);
        $this->assertEquals($value, $this->model->getUpdatedAt());
    }
}
