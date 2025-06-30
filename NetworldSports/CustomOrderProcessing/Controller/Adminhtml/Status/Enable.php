<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\ResultInterface;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;

/**
 * Enable a custom order status
 */
class Enable extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /** @see _isAllowed() */
    public const ADMIN_RESOURCE = 'NetworldSports_CustomOrderProcessing::status_enable';

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $repository;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @param Action\Context                         $context
     * @param CustomOrderStatusRepositoryInterface   $repository
     * @param TypeListInterface                      $cacheTypeList
     */
    public function __construct(
        Action\Context $context,
        CustomOrderStatusRepositoryInterface $repository,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->repository     = $repository;
        $this->cacheTypeList  = $cacheTypeList;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $statusId = (int)$this->getRequest()->getParam('status_id');
        if ($statusId) {
            try {
                $status = $this->repository->getById($statusId);
                $status->setIsEnabled(true);
                $this->repository->save($status);
                $this->messageManager->addSuccessMessage(__('Status enabled.'));
                // Invalidate cache
                $this->cacheTypeList->invalidate(CustomOrderStatus::CACHE_TAG);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
