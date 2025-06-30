<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface;

/**
 * Custom Order Status CRUD interface
 *
 * @api
 */
interface CustomOrderStatusRepositoryInterface
{
    /**
     * Save custom order status
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface $customOrderStatus
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CustomOrderStatusInterface $customOrderStatus): CustomOrderStatusInterface;

    /**
     * Retrieve custom order status by ID
     *
     * @param int $statusId
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $statusId): CustomOrderStatusInterface;

    /**
     * Retrieve custom order status by name
     *
     * @param string $statusName
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByName(string $statusName): CustomOrderStatusInterface;

    /**
     * Retrieve custom order statuses matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CustomOrderStatusSearchResultsInterface;

    /**
     * Delete custom order status
     *
     * @param \NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterface $customOrderStatus
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CustomOrderStatusInterface $customOrderStatus): bool;

    /**
     * Delete custom order status by ID
     *
     * @param int $statusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $statusId): bool;
}
