<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Test\Unit\Model\Source;

use NetworldSports\CustomOrderProcessing\Model\Source\IsEnabled;
use PHPUnit\Framework\TestCase;

/**
 * Is Enabled Source Model Test
 */
class IsEnabledTest extends TestCase
{
    /**
     * @var IsEnabled
     */
    private IsEnabled $model;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->model = new IsEnabled();
    }

    /**
     * Test to option array
     */
    public function testToOptionArray(): void
    {
        $expected = [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')]
        ];

        $result = $this->model->toOptionArray();

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['value']);
        $this->assertEquals('Yes', (string)$result[0]['label']);
        $this->assertEquals(0, $result[1]['value']);
        $this->assertEquals('No', (string)$result[1]['label']);
    }
}
