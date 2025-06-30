<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model\Source;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\Config as OrderConfig;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface;
use NetworldSports\CustomOrderProcessing\Model\Source\StatusOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Status Options Source Model Test
 */
class StatusOptionsTest extends TestCase
{
    /**
     * @var StatusOptions
     */
    private StatusOptions $model;

    /**
     * @var OrderConfig|MockObject
     */
    private $orderConfigMock;

    /**
     * @var CustomOrderStatusRepositoryInterface|MockObject
     */
    private $customOrderStatusRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->orderConfigMock = $this->createMock(OrderConfig::class);
        $this->customOrderStatusRepositoryMock = $this->createMock(CustomOrderStatusRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->model = new StatusOptions(
            $this->orderConfigMock,
            $this->customOrderStatusRepositoryMock,
            $this->searchCriteriaBuilderMock
        );
    }

    /**
     * Test to option array
     */
    public function testToOptionArray(): void
    {
        // Core statuses
        $coreStatuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'complete' => 'Complete'
        ];

        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn($coreStatuses);

        // Custom statuses
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchResultsMock = $this->createMock(CustomOrderStatusSearchResultsInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('is_enabled', 1)
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->customOrderStatusRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);

        $customStatus1 = $this->createMock(CustomOrderStatusInterface::class);
        $customStatus1->expects($this->once())
            ->method('getStatusName')
            ->willReturn('Custom Shipped');

        $customStatus2 = $this->createMock(CustomOrderStatusInterface::class);
        $customStatus2->expects($this->once())
            ->method('getStatusName')
            ->willReturn('Custom Processing');

        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$customStatus1, $customStatus2]);

        $result = $this->model->toOptionArray();

        $this->assertCount(5, $result);

        // Check core statuses
        $this->assertEquals('pending', $result[0]['value']);
        $this->assertEquals('Pending (Core)', $result[0]['label']);
        $this->assertEquals('processing', $result[1]['value']);
        $this->assertEquals('Processing (Core)', $result[1]['label']);
        $this->assertEquals('complete', $result[2]['value']);
        $this->assertEquals('Complete (Core)', $result[2]['label']);

        // Check custom statuses
        $this->assertEquals('Custom Shipped', $result[3]['value']);
        $this->assertEquals('Custom Shipped (Custom)', $result[3]['label']);
        $this->assertEquals('Custom Processing', $result[4]['value']);
        $this->assertEquals('Custom Processing (Custom)', $result[4]['label']);
    }
}
