<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus as ResourceModel;

/**
 * Custom Order Status Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'status_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'networldsports_custom_order_status_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'custom_order_status_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CustomOrderStatus::class, ResourceModel::class);
    }
}
