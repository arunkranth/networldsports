<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;
use NetworldSports\CustomOrderProcessing\Model\ResourceModel\CustomOrderStatus\CollectionFactory;

/**
 * Mass Enable Controller
 */
class MassEnable extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'NetworldSports_CustomOrderProcessing::status_enable';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;

            foreach ($collection->getItems() as $status) {
                $status->setIsEnabled(true);
                $this->customOrderStatusRepository->save($status);
                $count++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been enabled.', $count)
            );

            // Invalidate cache
            $this->cacheTypeList->invalidate(CustomOrderStatus::CACHE_TAG);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
