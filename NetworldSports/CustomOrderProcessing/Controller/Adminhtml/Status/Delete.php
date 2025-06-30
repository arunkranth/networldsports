<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;

/**
 * Delete Controller
 */
class Delete extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'NetworldSports_CustomOrderProcessing::status_delete';

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
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
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
        $resultRedirect = $this->resultRedirectFactory->create();
        $statusId = (int)$this->getRequest()->getParam('status_id');

        if ($statusId) {
            try {
                $this->customOrderStatusRepository->deleteById($statusId);
                $this->messageManager->addSuccessMessage(__('The status has been deleted.'));

                // Invalidate cache
                $this->cacheTypeList->invalidate(CustomOrderStatus::CACHE_TAG);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
