<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Block\Adminhtml\Status\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Generic Button Block
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * Get status ID
     *
     * @return int|null
     */
    public function getStatusId(): ?int
    {
        $model = $this->registry->registry('custom_order_status');
        return $model ? (int)$model->getId() : null;
    }

    /**
     * Generate URL by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
